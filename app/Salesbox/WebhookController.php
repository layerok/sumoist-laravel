<?php

namespace App\Salesbox;

use App\Salesbox\Events\SalesboxWebhookReceived;
use Illuminate\Support\Facades\Log;

class WebhookController {
    public function __invoke() {
        try {
            event(new SalesboxWebhookReceived());
            return response('ok', 200);
        } catch (\Exception $exception) {
            $error = $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
            Log::error($error);
            return response($error, 200);
        }
    }
}
