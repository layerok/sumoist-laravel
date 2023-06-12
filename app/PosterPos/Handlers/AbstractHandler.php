<?php

namespace App\PosterPos\Handlers;

abstract class AbstractHandler implements IHandler {
    public $params;
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function getAction() {
        return $this->params['action'];
    }

    public function getObjectId() {
        return $this->params['object_id'];
    }

    public function isRemoved(): bool {
        return $this->getAction() === 'removed';
    }

    public function isAdded(): bool {
        return $this->getAction() === 'added';
    }

    public function isChanged(): bool {
        return $this->getAction() === 'changed';
    }

    public function isTest(): bool {
        return $this->getAction() === 'test';
    }

}
