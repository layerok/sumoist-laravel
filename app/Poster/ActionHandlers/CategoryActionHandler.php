<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxCategory;

class CategoryActionHandler extends AbstractActionHandler {
    public function handle(): bool
    {
        if($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxCategory::sync($this->getObjectId());
        } else if($this->isRemoved()) {
            SalesboxCategory::delete($this->getObjectId());
        }

        return true;
    }

}
