<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ColorModel extends Model
{
    use HasFactory;
    use LogsActivity;
    protected static $logAttributes = ['name', 'description'];
    protected $table = 'colors';
    protected $fillable = [
        'name', 'description'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'description'])
        ->useLogName('colors');
        // Chain fluent methods for configuration options
    }
}
