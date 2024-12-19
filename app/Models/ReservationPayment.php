<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationPayment extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'tblReservationPayment';
    public $timestamps = false; 
    // Fillable Fields for Mass Assignment
    protected $fillable = [
        'ReservationID',
        'ActualPrice',
        'ExtraSeatPrice',
        'ExtendedTimePrice',
        'TotalTime',
        'DiscountPercentage',
        'DiscountPrice',
        'TaxPercentage',
        'TaxPrice',
        'TotalPrice',
        'status',
        'Added_By',
        'Updated_By',
    ];

    // Default Attributes
    protected $attributes = [
        'DiscountPercentage' => 0,
        'DiscountPrice' => 0,
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(TblTableReservation::class, 'ReservationID', 'id');
    }

    // Accessors and Mutators
    public function setTotalPriceAttribute()
    {
        $this->attributes['TotalPrice'] = 
            $this->ActualPrice + 
            $this->ExtraSeatPrice + 
            $this->ExtendedTimePrice + 
            $this->TaxPrice - 
            $this->DiscountPrice;
    }

}
