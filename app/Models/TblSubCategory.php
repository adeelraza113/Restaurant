<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSubCategory extends Model
{
    protected $table = 'tblsubcategory';
    public $timestamps = false;
    protected $fillable = ['SubCategoryName', 'Added_By', 'AddedDateTime', 'Updated_By', 'UpdatedDateTime', 'Revision'];
}
