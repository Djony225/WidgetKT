<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetParameter extends Model
{
    //

    protected $fillable = [
        'name',
        'type',
        'widget_id',
    ];

    function widget() {
        return $this->belongsTo(Widget::class);
    }
    
    
}