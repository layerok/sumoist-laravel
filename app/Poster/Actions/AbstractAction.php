<?php

namespace App\Poster\Actions;

abstract class AbstractAction implements IAction{
   public $params;
   public function __construct($params) {
       $this->params = $params;
   }
    public function getObjectId() {
        return $this->params['object_id'];
    }
}
