<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblCategory extends Model
{
    protected $table = 'tblcategory';
    public $timestamps = false;
    protected $fillable = ['CategoryName', 'Added_By', 'AddedDateTime', 'Updated_By', 'UpdatedDateTime', 'Revision'];
}
