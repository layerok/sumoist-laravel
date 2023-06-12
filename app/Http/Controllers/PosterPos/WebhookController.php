<?php
namespace App\Http\Controllers\PosterPos;

use App\Http\Controllers\Controller;
use App\PosterPos\Webhook;
use \Illuminate\Http\Request;

class WebhookController extends Controller
{
   public function __invoke(Request $request) {
       $webhook = new Webhook();
       return $webhook->handle($request);
   }
}
