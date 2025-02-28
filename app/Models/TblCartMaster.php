<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblCartMaster extends Model
{
    protected $table = 'tblcartmaster';

    public $timestamps = false; // Disables automatic timestamps

    protected $fillable = [
        'User_id',
        'ReservationID',
        'Discount',
        'PaymentStatus',
        'TotalAmount',
        'AddedBy',
        'AddedDateTime',
        'UpdatedBy',
        'UpdatedDateTime',
        'Revision',
        'MachineName'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    public function reservation()
    {
        return $this->belongsTo(TblTableReservation::class, 'ReservationID');
    }
}
