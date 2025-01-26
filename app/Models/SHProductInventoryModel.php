<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SHProductInventoryModel extends Model
{
    protected $connection = "mysql_sh_prefix";
    protected $table = "product_inventories";
    protected $fillable = ['product_id','sku','stock_count','sold_count','created_at','updated_at'];

    public function getTable()
    {
        return $this->table;
    }
}
