<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable = [
        'name',
        'is_public',
    ];


    function widgets() {
        return $this->hasMany(Widget::class);
    }
    
}