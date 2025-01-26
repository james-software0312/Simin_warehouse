<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class CategoryModel extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static $logAttributes = ['name', 'parent_id', 'description'];
    protected $table = 'category';
    

    protected $fillable = [
        'name', 'parent_id', 'description'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'parent_id','description'])
        ->useLogName('category');
        // Chain fluent methods for configuration options
    }

    // Define the parent category relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Define the children category relationship
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
