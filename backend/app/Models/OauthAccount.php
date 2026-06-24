<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthAccount extends Model
{
    //
    protected $fillable = [
        'provider',
        'provider_user_id',
        'token',
        'user_id',
    ];

    function user() {
        return $this->belongsTo(User::class);
    }

    
}