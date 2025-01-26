<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SellHideHistoryModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['sell_reference', 'purchase_transaction_id', 'hidden_amount'];
    protected $table = 'sell_hide_history';

    protected $fillable = [
        'sell_reference', 'purchase_transaction_id', 'hidden_amount'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['sell_reference', 'purchase_transaction_id', 'hidden_amount'])
        ->useLogName('sell_hide_history');
        // Chain fluent methods for configuration options
    }
}
