<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SHMediaUploadModel extends Model
{
    protected $connection = "mysql_sh_prefix";
    protected $table = "media_uploads";
    protected $fillable = ['title','alt','size','path','dimensions','user_id'];

    public function getTable()
    {
        return $this->table;
    }
}
