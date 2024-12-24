<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'tblVendor';
    public $timestamps = false;
    protected $fillable = [
        'VendorName',
        'Email',
        'Contact',
        'Address',
        'Fax',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'Revision',
        'MachineName',
    ];

    
}
