<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpotsController extends Controller
{
    public function get(){

        $spots = DB::table('spots')->select('id', 'slug', 'name')->get();

        return $spots;

    }

    public function getOne($slug){

        $spot = DB::table('spots')->select('id', 'slug', 'name')->where('slug' , $slug)->get();

        return $spot;

    }
}
