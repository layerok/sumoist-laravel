<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * @var string
     */
    protected $table = 'payment';

    /**
     * @var array
     */
    protected $fillable = [
        "name", "hidden", "id"
    ];

    protected $casts = [
        "hidden" => "boolean"
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
