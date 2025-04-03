<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'OrderItems';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'OrderNo',
        'ProductID',
        'Price',
        'Quantity',
        'TaxPrice',
        'DiscountPrice',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderNo', 'OrderNo');
    }
}
