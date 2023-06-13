<?php
namespace App\Poster;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Webhook {
    public function handle(Request $request) {
        if(!$this->isVerifiedRequest($request)) {
            Log::error("Request signatures didn't match for PosterPos webhook: " . $request->getContent());
            return response("Request signatures didn't match!", 403);
        }

        $parsed = json_decode($request->getContent(), true);

        // some meta programming below
        $class = 'App\\Poster\\Actions\\' . studly_case($parsed['object'] . '_' . $parsed['action']) . 'Action';

        if(class_exists($class)) {
            $instance = new $class($parsed);
            return $instance->handle();
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
