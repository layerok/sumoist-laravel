<?php

namespace App\Poster;

use App\Poster\Events\PosterWebhookReceived;
use App\Poster\Facades\PosterStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use poster\src\PosterApi;

class WebhookController
{
    public function __invoke(Request $request)
    {
        PosterStore::init();
        $content = $request->getContent();
        $decoded = json_decode($content, true);

        // todo: don't forget to delete 'dev' param
        $isVerified = isset($decoded['dev']) || PosterApi::auth()->verifyWebHook($content);

        if (!$isVerified) {
            $error = "Request signatures didn't match!";
            Log::error($error . $request->getContent());
            return response($error, 200);
        }
        try {
            $params = json_decode($request->getContent(), true);
            event(new PosterWebhookReceived($params));
            return response('ok');
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
            return response('Error: ' . $exception->getMessage(), 200);
        }
    }
}
