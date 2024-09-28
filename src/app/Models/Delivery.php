<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    /**
     * @var string
     */
    protected $table = 'delivery';

    /**
     * @var array
     */
    protected $fillable = [
        "name", "price"
    ];

    protected $casts = [
        "hidden" => "boolean"
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
