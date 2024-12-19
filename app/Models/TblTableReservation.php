<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblTableReservation extends Model
{
    use HasFactory;

    protected $table = 'tblTableReservation';
    public $timestamps = false;
    protected $fillable = [
        'UserID',
        'SittingTableID',
        'SittingPlan',
        'ReservationNumber',
        'StartTime',
        'EndTime',
        'ExtendedTime',
        'Added_By',
        'Updated_By',
        'Revision',
    ];
}
