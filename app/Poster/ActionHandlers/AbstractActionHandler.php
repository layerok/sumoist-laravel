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
}
