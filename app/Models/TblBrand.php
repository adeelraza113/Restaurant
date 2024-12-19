<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblBrand extends Model
{
    protected $table = 'tblbrand';
    public $timestamps = false;
    protected $fillable = ['BrandName', 'Added_By', 'AddedDateTime', 'Updated_By', 'UpdatedDateTime', 'Revision'];
}
