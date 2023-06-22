<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use RuntimeException;

class ProductActionHandler extends AbstractActionHandler
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

            if ($poster_product->hasModifications()) {
                $instance = new ProductMultipleActionHandler($this->params);
                $instance->handle();
            } else {
                $instance = new ProductSingleActionHandler($this->params);
                $instance->handle();
            }


        }
        return true;
    }

}
