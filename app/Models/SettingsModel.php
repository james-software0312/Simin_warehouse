<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SettingsModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['company', 'pagename', 'language', 'datetime', 'timezone','logo',  'company_email', 'company_phone', 'company_address'];
    protected $table = 'settings';

    protected $fillable = [
        'company', 'pagename', 'language', 'datetime', 'timezone','logo' ,  'company_email', 'company_phone', 'company_address'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['company', 'pagename', 'language', 'datetime', 'timezone','logo' ,  'company_email', 'company_phone', 'company_address'])
        ->useLogName('settings');
        // Chain fluent methods for configuration options
    }
}
