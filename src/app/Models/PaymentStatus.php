<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    /**
     * @var string
     */
    protected $table = 'payment_statuses';

    /**
     * @var array
     */
    protected $fillable = [
        "name", "key"
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }



}
