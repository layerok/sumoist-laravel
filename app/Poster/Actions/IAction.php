<?php

namespace App\Poster\Actions;

use Illuminate\Http\Response;

interface IAction {
    public function handle():Response;
}
