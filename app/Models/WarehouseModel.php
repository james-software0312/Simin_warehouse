<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WarehouseModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['name', 'description', 'is_primary'];
    protected $table = 'warehouse';

    protected $fillable = [
         'name', 'description', 'is_primary'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'description', 'is_primary'])
        ->useLogName('warehouse');
        // Chain fluent methods for configuration options
    }
}
