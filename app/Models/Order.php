<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;


class Order extends Model
{
    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var array
     */
    protected $fillable = [
        "user_id", "id", "first_name", "email", "address", "phone", "comment", "payment_id", "delivery_id", "payment_status_id", "sum", "is_sent_to_poster", "created_at"
    ];

    protected $casts  = [
        'is_sent_to_poster' =>  'boolean'
    ];
    public function getCreatedAtAttribute($value)
    {
        $date = new Date("{$value}");
        return $date->format('j F, H:i');
    }


    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function payment_status()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }


}
