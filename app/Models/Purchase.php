<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'tblPurchase';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'VendorID', 'ProductID', 'InvoiceNo', 'BatchNo', 'Qty', 'UnitPrice',
        'TaxPercentage', 'DiscountPercentage',
        'Remarks', 'Lock', 'IssuedtoStore', 'InvoiceDate', 'ActualDate', 'ExpiryDate',
        'Stockin_By', 'Added_By', 'AddedDateTime','Updated_By','UpdatedDateTime', 'MachineName', 'Revision'
    ];

    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'VendorID');
    }

    public function product()
    {
        return $this->belongsTo(TblProducts::class, 'ProductID');
    }
}
