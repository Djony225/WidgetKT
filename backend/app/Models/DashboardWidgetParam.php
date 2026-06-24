<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardWidgetParam extends Model
{
    //
    protected $fillable = [
        'param_name',
        'param_value',
        'dashboard_widget_id',
    ];

    function dashboardWidget() {
        return $this->belongsTo(DashboardWidget::class);
    }
    
    

    
}