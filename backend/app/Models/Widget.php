<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'service_id',
    ];

    function service() {
        return $this->belongsTo(Service::class);
    }
    function parameters() {
        return $this->hasMany(WidgetParameter::class);
    }
    
    
}