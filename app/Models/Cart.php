<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'tblCart';
    public $timestamps = false;
    protected $fillable = [
        'UserID',
        'ReservationID',
        'ProductID',
        'SittingTableID',
        'UnitPrice',
        'Qty',
        'TaxPrice',
        'DiscountPrice',
        'PaymentStatus',
        'OrderType',
        'Added_By',
        'AddedDateTime',
        'MachineName',
        'Revision',
        'Updated_By',
        'UpdatedDateTime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function reservation()
    {
        return $this->belongsTo(TblTableReservation::class, 'ReservationID');
    }

    public function product()
    {
        return $this->belongsTo(TblProducts::class, 'ProductID');
    }

    public function sittingTable()
    {
        return $this->belongsTo(TblSittingTableS::class, 'SittingTableID');
    }
}
