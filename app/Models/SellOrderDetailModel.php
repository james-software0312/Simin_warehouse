<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SellOrderDetailModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['stockitemid', 'warehouseid', 'contactid', 'reference','selldate','quantity','unitid','description', 'price', 'discount'];
    protected $table = 'sell_order_detail';

    protected $fillable = [
        'stockitemid', 'warehouseid', 'contactid','reference','selldate','quantity','unitid','description', 'price', 'discount'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['stockitemid', 'warehouseid', 'contactid','reference','selldate','quantity','unitid','description', 'price', 'discount'])
        ->useLogName('sell_order_detail');
        // Chain fluent methods for configuration options
    }
}
