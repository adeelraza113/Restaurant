<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMaster extends Model
{
    protected $table = 'tblPurchaseMaster';

    protected $fillable = [
        'VendorID',
        'InvoiceNo',
        'BatchNo',
        'TotalTax',
        'TotalDiscount',
        'TotalAmount',
        'MasterRemarks',
        'Lock',
        'IssuedtoStore',
        'InvoiceDate',
        'ActualDate',
        'Stockin_By',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'Revision',
        'MachineName'
    ];

    public $timestamps = false;
}
