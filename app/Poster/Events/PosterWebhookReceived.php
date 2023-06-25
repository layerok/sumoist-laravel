<?php

namespace App\Poster\Events;

class PosterWebhookReceived {
    public $params;
    public $object;
    public $object_id;
    public $action;
    public $account;
    public $verify;
    public $data;
    public function __construct($params) {
        $this->params = $params;
        $this->object = $params['object'];
        $this->object_id = $params['object_id'];
        $this->action = $params['action'];
        $this->account = $params['account'];
        $this->verify = $params['verify'];
        $this->data = $params['data'];
    }
}
