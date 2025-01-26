<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionModel extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected static $logAttributes = ['stockitemid', 'contactid', 'warehouseid', 'reference','status','transactiondate','quantity','description', 'price', 'unitid', 'hidden_amount', 'creator'];
    protected $table = 'transaction';

    protected $fillable = [
        'stockitemid', 'contactid', 'warehouseid', 'reference','status','transactiondate','quantity','description', 'price', 'unitid', 'hidden_amount', 'creator'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['stockitemid', 'contactid', 'warehouseid', 'reference','status','transactiondate','quantity','description', 'price', 'unitid', 'hidden_amount', 'creator'])
        ->useLogName('transaction');
        // Chain fluent methods for configuration options
    }
}
