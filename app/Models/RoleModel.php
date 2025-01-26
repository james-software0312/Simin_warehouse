<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RoleModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['userid', 'module', 'permission'];
    protected $table = 'role';

    protected $fillable = [
        'userid', 'module', 'permission'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['userid', 'module', 'permission'])
        ->useLogName('role');
        // Chain fluent methods for configuration options
    }
}
