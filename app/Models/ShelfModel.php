<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ShelfModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['code', 'name','warehouseid', 'description'];
    protected $table = 'shelf';

    protected $fillable = [
        'code', 'warehouseid', 'name', 'description'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['code', 'name', 'description'])
        ->useLogName('shelf');
        // Chain fluent methods for configuration options
    }
}
