<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MovementModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['stockitemid', 'code', 'reference', 'source_warehouse_id', 'target_warehouse_id', 'movement_date', 'quantity',  'price', 'unitid', 'description', 'status', 'creator'];
    protected $table = 'movement';

    protected $fillable = [
        'stockitemid', 'code', 'reference', 'source_warehouse_id', 'target_warehouse_id', 'movement_date', 'quantity',  'price', 'unitid', 'description', 'status', 'creator'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['stockitemid', 'code', 'reference', 'source_warehouse_id', 'target_warehouse_id', 'movement_date', 'quantity',  'price', 'unitid', 'description', 'status', 'creator'])
        ->useLogName('movement');
        // Chain fluent methods for configuration options
    }
}
