<?php

namespace App\Poster\Stores;

class RootStore {
    /** @var SalesboxStore $salesboxStore */
    private $salesboxStore;

    /**  @var PosterStore $posterStore */
    private $posterStore;

    public function __construct() {
        $this->salesboxStore = new SalesboxStore($this);
        $this->posterStore = new PosterStore($this);
    }

    public function getSalesboxStore(): SalesboxStore {
        return $this->salesboxStore;
    }

    public function getPosterStore(): PosterStore {
        return $this->posterStore;
    }

}
