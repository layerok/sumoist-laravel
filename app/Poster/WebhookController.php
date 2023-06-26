<?php

namespace App\Poster;

use App\Poster\Events\Application\PosterApplicationTest;
use App\Poster\Events\Category\PosterCategoryAdded;
use App\Poster\Events\Category\PosterCategoryChanged;
use App\Poster\Events\Category\PosterCategoryActionPerformed;
use App\Poster\Events\Category\PosterCategoryRemoved;
use App\Poster\Events\Category\PosterCategoryRestored;
use App\Poster\Events\Dish\PosterDishAdded;
use App\Poster\Events\Dish\PosterDishChanged;
use App\Poster\Events\Dish\PosterDishActionPerformed;
use App\Poster\Events\Dish\PosterDishRemoved;
use App\Poster\Events\Dish\PosterDishRestored;
use App\Poster\Events\PosterWebhookReceived;
use App\Poster\Events\Product\PosterProductAdded;
use App\Poster\Events\Product\PosterProductChanged;
use App\Poster\Events\Product\PosterProductActionPerformed;
use App\Poster\Events\Product\PosterProductRemoved;
use App\Poster\Events\Product\PosterProductRestored;
use App\Poster\Facades\PosterStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use poster\src\PosterApi;

class WebhookController
{
    protected $events = [
        'application.test' => PosterApplicationTest::class,

        'category' => PosterCategoryActionPerformed::class,
        'category.removed' => PosterCategoryRemoved::class,
        'category.added' => PosterCategoryAdded::class,
        'category.changed' => PosterCategoryChanged::class,
        'category.recovered' => PosterCategoryRestored::class,

        'product' => PosterProductActionPerformed::class,
        'product.removed' => PosterProductRemoved::class,
        'product.added' => PosterProductAdded::class,
        'product.changed' => PosterProductChanged::class,
        'product.recovered' => PosterProductRestored::class,

        'dish' => PosterDishActionPerformed::class,
        'dish.removed' => PosterDishRemoved::class,
        'dish.added' => PosterDishAdded::class,
        'dish.changed' => PosterDishChanged::class,
        'dish.recovered' => PosterDishRestored::class,

    ];

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

            $specificEvent = $this->events["{$params['object']}.{$params['action']}"];
            if ($specificEvent) {
                event(new $specificEvent($params));
            }

            $commonEvent = $this->events[$params['object']];

            if($commonEvent) {
                event(new $commonEvent($params));
            }
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
