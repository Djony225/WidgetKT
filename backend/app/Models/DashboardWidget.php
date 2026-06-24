<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardWidget extends Model
{
    protected $fillable = [
        'refresh_rate',
        'position',
        'user_id',
        'widget_id',
    ];

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function configuredParams(): HasMany
    {
        return $this->hasMany(DashboardWidgetParam::class, 'dashboard_widget_id');
    }
}
