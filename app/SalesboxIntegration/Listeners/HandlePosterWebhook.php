<?php

namespace App\SalesboxIntegration\Listeners;

use App\Poster\Events\PosterWebhookReceived;
use App\SalesboxIntegration\Handlers\CategoryHandler;
use App\SalesboxIntegration\Handlers\CategoryRemovedHandler;
use App\SalesboxIntegration\Handlers\DishHandler;
use App\SalesboxIntegration\Handlers\DishRemovedHandler;
use App\SalesboxIntegration\Handlers\ProductHandler;
use App\SalesboxIntegration\Handlers\ProductRemovedHandler;
use Illuminate\Http\Request;

class HandlePosterWebhook
{
    public $request;

    public $handlers = [
        'dish' => DishHandler::class,
        'dish.removed' => DishRemovedHandler::class,
        'product' => ProductHandler::class,
        'product.removed' => ProductRemovedHandler::class,
        'category' => CategoryHandler::class,
        'category.removed' => CategoryRemovedHandler::class,
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(PosterWebhookReceived $event)
    {
        if(isset($this->handlers[$event->object . '.' . $event->action])) {
            $class = $this->handlers[$event->object . '.' . $event->action];
            $instance = new $class($event->params);
            $instance->handle();
            return true;
        }

        if(isset($this->handlers[$event->object])) {
            $class = $this->handlers[$event->object];
            $instance = new $class($event->params);
            $instance->handle();
            return true;
        }

        return true;
    }


}
