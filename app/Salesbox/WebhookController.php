<?php

namespace App\Salesbox;

use App\Salesbox\Events\SalesboxOrderCreated;
use App\Salesbox\Events\SalesboxWebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController {
    public function __invoke(Request $request) {
        try {
            event(new SalesboxWebhookReceived());
            event(new SalesboxOrderCreated());
            return response('ok', 200);
        } catch (\Exception $exception) {
            $error = $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
            Log::error($error);
            Log::error($request->getContent());
            return response($error, 200);
        }
    }
}
