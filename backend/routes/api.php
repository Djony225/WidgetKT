<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\DashboardWidgetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\WidgetDataController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/about.json', [AboutController::class, 'index']);

Route::middleware('auth:sanctum')->group(function (){
    //routes service et widget
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::get('/widgets', [WidgetController::class, 'index']);
    Route::get('/widgets/{widget}', [WidgetController::class, 'show']);


    //routes dashboard widget
    Route::get('/dashboard', [DashboardWidgetController::class, 'index']);
    Route::post('/dashboard', [DashboardWidgetController::class, 'store']);
    Route::put('/dashboard/{dashboardWidget}', [DashboardWidgetController::class, 'update']);
    Route::delete('/dashboard/{dashboardWidget}', [DashboardWidgetController::class, 'destroy']);
   
    // widget real data from api call
    Route::get('/widgets/{widget}/data', [WidgetDataController::class, 'getData']);
    
    
    //routes admin
    Route::middleware('isAdmin')->group(function(){
        Route::get('/admin/users', [AdminController::class, 'index']);
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser']);
        
    });
});