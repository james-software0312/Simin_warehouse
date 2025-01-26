<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SHProductCategoryModel extends Model
{
    use HasFactory;
    protected $connection = "mysql_sh_prefix";
    protected $table = 'product_categories';

    protected $fillable = [
        'title',
        'status',
        'parent_id',
        'image'
    ];

    public function getTable()
    {
        return $this->table;
    }
}
