<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblSittingTableS extends Model
{
    use HasFactory;

    protected $table = 'tblsittingtables';

    public $timestamps = false; 
    
    protected $fillable = [
         'TableName',
        'TableNo',
        'SittingCapacity',
        'SittingPlan',
        'TableTypeID',
        'ActualPrice',
        'ExtraSeatPrice',
        'ExtendedPricePerHour',
        'isReserved',
        'show',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'ImageName',
        'ImagePath',
        'Revision'
    ];
}
