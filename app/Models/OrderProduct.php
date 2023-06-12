<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    /**
     * @var string
     */
    protected $table = 'orders_products';

    /**
     * @var array
     */
    protected $fillable = [
        "order_id", "product_id", "attribute_value_id", "quantity", "sum", "price"
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id');
    }
}
