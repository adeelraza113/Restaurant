<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAllPayments extends Model
{
    protected $table = 'tblAllPayments';
    public $timestamps = false;
    protected $fillable = [
        'ReservationID',
        'ReservationPaymentID',
        'TotalPayment',
        'CashPayment',
        'CardPayment',
        'InvoiceNo',
        'Added_By',
        'Updated_By',
        'MachineName',
        'Revision'
    ];
}
