<?php

namespace App\Poster\Actions;

use Illuminate\Http\Response;

class ApplicationTestAction extends AbstractAction  {
    public function handle(): Response
    {
        return response('ok', 200);
    }
}
