<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SellOrderModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = [ 'warehouseid', 'contactid', 'reference','selldate','description', 'discount', 'discount_type', 'confirmed', 'withinvoice', 'hidden', 'signed', 'creator', 'payment_type', 'show_reference', 'pre_order'];
    protected $table = 'sell_order';

    protected $fillable = [
        'warehouseid', 'contactid','reference','selldate','description', 'discount', 'discount_type', 'confirmed', 'withinvoice', 'hidden', 'signed', 'creator', 'payment_type', 'show_reference', 'pre_order'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([ 'warehouseid', 'contactid','reference','selldate','description', 'discount', 'discount_type', 'confirmed', 'withinvoice', 'hidden', 'signed', 'creator', 'payment_type', 'show_reference', 'pre_order'])
        ->useLogName('sell_order');
        // Chain fluent methods for configuration options
    }
}
