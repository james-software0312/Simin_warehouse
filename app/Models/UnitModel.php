<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UnitModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['code', 'name', 'description'];
    protected $table = 'unit';

    protected $fillable = [
        'code', 'name', 'description'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['code', 'name', 'description'])
        ->useLogName('unit');
        // Chain fluent methods for configuration options
    }
}
