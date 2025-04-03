<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    protected $table = 'Orders';
    public $timestamps = false;
    protected $primaryKey = 'OrderNo';

    protected $fillable = [
        'UserID',
        'SittingTableID',
        'ReservationID',
        'TotalPrice',
        'PaymentStatus',
        'OrderType',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'MachineName',
        'Revision',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'OrderNo', 'OrderNo');
    }
}
