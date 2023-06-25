<?php

namespace App\SalesboxIntegration\Handlers;

use App\Poster\Facades\PosterStore;
use RuntimeException;

class DishHandler extends AbstractHandler
{
    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            if (!PosterStore::isProductsLoaded()) {
                PosterStore::loadProducts();
            }

            if (!PosterStore::productExists($this->getObjectId())) {
                throw new RuntimeException(sprintf('product#%s is not found in poster', $this->getObjectId()));
            }

            $poster_product = PosterStore::findProduct($this->getObjectId());

            if ($poster_product->hasDishModificationGroups()) {
                $instance = new DishMultipleHandler($this->params);
                $instance->handle();
            } else {
                $instance = new DishSingleHandler($this->params);
                $instance->handle();
            }

        }

        return true;

    }
}
