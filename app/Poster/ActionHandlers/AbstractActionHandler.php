<?php

namespace App\Poster\ActionHandlers;

abstract class AbstractActionHandler implements IAction {
   public $params;
   public function __construct($params) {
       $this->params = $params;
   }
    public function getObjectId() {
        return $this->params['object_id'];
    }

    public function getAction() {
       return $this->params['action'];
    }

    public function isRemoved(): bool {
       return $this->getAction() === 'removed';
    }

    public function isAdded(): bool {
        return $this->getAction() === 'added';
    }

    public function isRestored(): bool {
        return $this->getAction() === 'restored';
    }

    public function isChanged(): bool {
        return $this->getAction() === 'changed';
    }
}
