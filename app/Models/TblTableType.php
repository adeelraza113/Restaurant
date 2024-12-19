<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblTableType extends Model
{
    use HasFactory;

    protected $table = 'tbltabletype';
    public $timestamps = false; 

    protected $fillable = [
        'Table_Type',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'Revision',
    ];
}
