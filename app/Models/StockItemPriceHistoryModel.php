<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockItemPriceHistoryModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['stockitem_id', 'price', 'creator_id'];
    protected $table = 'stockitem_price_history';

    protected $fillable = [
        'stockitem_id', 'price', 'creator_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['stockitem_id', 'price', 'creator_id'])
        ->useLogName('stockitem_price_history');
        // Chain fluent methods for configuration options
    }
}
