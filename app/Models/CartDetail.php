<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $table = 'tblCartDetails'; // Specify table name

    protected $fillable = [
        'OrderNo',
        'ProductID',
        'UnitPrice',
        'Qty',
        'Discount',
        'TaxPercentage',
    ];

    
    public function cartMaster()
    {
        return $this->belongsTo(TblCartMaster::class, 'OrderNo', 'OrderNo');
    }

   
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'id');
    }
}
