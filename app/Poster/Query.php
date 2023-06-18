<?php

namespace App\Poster;

class Query {
    public $key;
    public $queryFn;
    public function __construct(array $key, \Closure $queryFn)
    {
        $this->key = implode($key, '.');
        $this->queryFn = $queryFn;
    }
}
