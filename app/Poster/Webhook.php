<?php
namespace App\Poster;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use poster\src\PosterApi;

class Webhook {
    public function handle(Request $request) {
        $config = config('poster');
        PosterApi::init([
            'application_id' => $config['application_id'],
            'application_secret' => $config['application_secret'],
            'account_name' => $config['account_name'],
            'access_token' => $config['access_token'],
        ]);
        $content = $request->getContent();
        $decoded = json_decode($content, true);

        // todo: don't forget to delete 'skip' param
        $isVerified = isset($decoded['skip']) || PosterApi::auth()->verifyWebHook($content);

        if(!$isVerified) {
            $error = "Request signatures didn't match!";
            Log::error($error . $request->getContent());
            return response($error, 200);
        }

        $parsed = json_decode($request->getContent(), true);

        // some meta programming below
        $namespace = 'App\\Poster\\ActionHandlers\\';
        $className = studly_case($parsed['object'] . '_action_handler'); // e.g. DishActionHandler

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


}
