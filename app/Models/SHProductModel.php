<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SHProductModel extends Model
{
    protected $connection = "mysql_sh_prefix";
    protected $table = 'products';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'category_id',
        'sub_category_id',
        'image',
        'product_image_gallery',
        'price',
        'sale_price',
        'badge',
        'status',
        'attributes',
        'sold_count',
        'color',
        'size',
    ];
}
