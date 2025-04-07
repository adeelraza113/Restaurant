<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $table = 'favourite';

    protected $fillable = ['UserID', 'ProductID', 'isFavourite'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

   
    public function product()
    {
        return $this->belongsTo(TblProducts::class, 'ProductID');
    }
}
