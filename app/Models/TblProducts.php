<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblProducts extends Model
{
    protected $table = 'tblproducts';

    protected $fillable = [
        'ProductCode',
        'ProductName',
        'AlternateName',
        'OtherName',
        'Barcode',
        'BID',
        'CID',
        'SCID',
        'SSCID',
        'BoxPerCtn',
        'PiecePerBox',
        'PurchasedPrice',
        'SalePrice1',
        'SalePrice2',
        'DiscountPercentage',
        'ActiveDiscount',
        'ExpiryDate',
        'RackNo',
        'ReorderLevel',
        'Qty',
        'ImagePath',
        'ImageName',
        'Added_By',
        'AddedDateTime',
        'Updated_By',
        'UpdatedDateTime',
        'Revision',
    ];

    
    public $timestamps = false;


    public function category()
    {
        return $this->belongsTo(TblCategory::class, 'CID');
    }

    public function subcategory()
    {
        return $this->belongsTo(TblSubCategory::class, 'SCID');
    }

    public function subsubcategory()
    {
        return $this->belongsTo(TblSubSubCategory::class, 'SSCID');
    }

    public function favourites()
{
    return $this->hasMany(Favourite::class, 'ProductID');
}


}
