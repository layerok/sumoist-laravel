<?php
namespace App\PosterPos;

use Illuminate\Http\Request;

class Webhook {
    public function handle(Request $request) {
        if(!$this->isVerifiedRequest($request)) {
            Log::error("Request signatures didn't match for PosterPos webhook: " . $request->getContent());
            return response("Request signatures didn't match!", 403);
        }

        $parsed = json_decode($request->getContent(), true);

        // some meta programming below
        $class = 'App\\PosterPos\\Handlers\\' . studly_case($parsed['object']) . 'Handler';


        if(class_exists($class)) {
            $instance = new $class($parsed);
            $instance->handle();
        } else {
            // skipping unhandled entities
            // todo: should we log such entities?
        }

        return response('ok', 200);
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
