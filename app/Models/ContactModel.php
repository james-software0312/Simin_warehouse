<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContactModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['name', 'surname', 'company', 'address', 'city', 'postal_code', 'country', 'email','phone', 'vat_number','whatsapp','status','description'];
    protected $table = 'contact';

    protected $fillable =  ['name', 'surname', 'company', 'address', 'city', 'postal_code', 'country', 'email','phone', 'vat_number','whatsapp','status','description'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly( ['name', 'surname', 'company', 'address', 'city', 'postal_code', 'country', 'email','phone', 'vat_number','whatsapp','status','description'])
        ->useLogName('contact');
        // Chain fluent methods for configuration options
    }
}
