<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSubSubCategory extends Model
{
    protected $table = 'tblsubsubcategory';
    public $timestamps = false;
    protected $fillable = ['SubSubCategoryName', 'Added_By', 'AddedDateTime', 'Updated_By', 'UpdatedDateTime', 'Revision'];
}
