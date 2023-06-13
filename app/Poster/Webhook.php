<?php
namespace App\Poster;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Webhook {
    public function handle(Request $request) {
        if(!$this->isVerifiedRequest($request)) {
            $error = "Request signatures didn't match!";
            Log::error($error . $request->getContent());
            return response($error, 200);
        }

        $parsed = json_decode($request->getContent(), true);

        // some meta programming below
        $namespace = 'App\\Poster\\ActionHandlers\\';
        $className = studly_case($parsed['object'] . '_' . $parsed['action'] . '_action_handler'); // e.g. DishCreatedAction
        $class = $namespace . $className;

        if(class_exists($class)) {
            $instance = new $class($parsed);
            try {
                $instance->handle();
                return response('ok', 200);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
                // I return successful response here,
                // because if poster doesn't get 200,
                // then it will exponentially backoff all next and retried requests

                // How poster retries requests?
                // 1 request - 'instantly'
                // 2 request - ~30 seconds
                // 3 request - ~1 minutes
                // 4 request - ~5 minutes
                // 5 request - ~10 minutes
                // n request - so on
                return response('not ok',200);
            }
        }

        return response('nothing was handled', 200);
    }

    function isVerifiedRequest(Request $request): bool {
        $parsed = json_decode($request->getContent(), true);

        $verify_original = $parsed['verify'];

        $verify = [
            $parsed['account'],
            $parsed['object'],
            $parsed['object_id'],
            $parsed['action'],
        ];

        // Если есть дополнительные параметры
        if (isset($parsed['data'])) {
            $verify[] = $parsed['data'];
        }
        $verify[] = $parsed['time'];
        $verify[] = config('poster.application_secret');

        // Создаём строку для верификации запроса клиентом
        $verify = md5(implode(';', $verify));

        // Сравниваем подписи
        return $verify === $verify_original;
    }
}
