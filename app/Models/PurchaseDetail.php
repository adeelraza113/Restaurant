<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'tblpurchasedetail';
    public $timestamps = false;
    protected $fillable = [
        'PurchaseMasterID',
        'ProductID',
        'Qty',
        'UnitPrice',
        'TaxPercentage',
        'TaxPrice',
        'DiscountPercentage',
        'DiscountPrice',
        'TotalPrice',
        'ExpiryDate',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'Revision',
        'MachineName',
    ];
}
