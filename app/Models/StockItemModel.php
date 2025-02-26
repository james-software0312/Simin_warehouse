<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockItemModel extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static $logAttributes = ['code', 'categoryid', 'unitid','warehouseid','name','quantity','single_quantity', 'quantity_website','photo','description','itemsubtype', 'price',  'contactid',  'purchase_price', 'vat', 'size', 'unitconverter', 'unitconverterto', 'hidden_amount', 'unitconverter1', 'is_visible', 'is_delete', 'product_id', 'color'];
    protected $table = 'stockitem';

    protected $fillable = [
        'code', 'categoryid', 'unitid','warehouseid','name','quantity','single_quantity', 'quantity_website','photo','description',  'itemsubtype', 'price', 'contactid',  'purchase_price', 'vat', 'size', 'unitconverter', 'unitconverterto', 'hidden_amount', 'unitconverter1', 'is_visible', 'is_delete', 'product_id', 'color'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['code', 'categoryid', 'unitid','warehouseid','name','quantity','single_quantity', 'quantity_website','photo','description',  'itemsubtype', 'price', 'contactid',  'purchase_price', 'vat', 'size', 'unitconverter', 'unitconverterto', 'hidden_amount', 'unitconverter1', 'is_visible', 'is_delete', 'product_id', 'color'])
        ->useLogName('stockitem');
        // Chain fluent methods for configuration options
    }
}
