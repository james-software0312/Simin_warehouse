<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionOrderModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['contactid', 'warehouseid', 'reference','status','transactiondate','description', 'signed', 'creator', 'confirmed', 'show_reference'];
    protected $table = 'transaction_order';

    protected $fillable = [
        'contactid', 'warehouseid', 'reference','status','transactiondate','description', 'signed', 'creator', 'confirmed', 'show_reference'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['contactid', 'warehouseid','reference','status','transactiondate','description', 'signed', 'creator', 'confirmed', 'show_reference'])
        ->useLogName('transaction_order');
        // Chain fluent methods for configuration options
    }
}
