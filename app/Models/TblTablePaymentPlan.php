<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblTablePaymentPlan extends Model
{
    use HasFactory;

    protected $table = 'tblTablePaymentPlan';
    public $timestamps = false;
    protected $fillable = [
        'SittingTableID',
        'PricePerHour',
        'PricePerExtraSeat',
        'Discount',
        'Added_By',
        'Updated_By',
        'Revision',
    ];
}
