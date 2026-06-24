<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DashboardWidget;
use App\Models\DashboardWidgetParam;
use Illuminate\Http\JsonResponse;
class DashboardWidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $widgets = DashboardWidget::with(['widget.parameters', 'configuredParams'])->where('user_id', $request->user()->id)->get();
        return response()->json($widgets);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'widget_id' => 'required|exists:widgets,id',
            'refresh_rate' => 'required|integer|min:10',
            'position' => 'required|integer',
            'params' => 'array',
        ]);

        $dashboardWidget = DashboardWidget::create([
            'widget_id' => $request->widget_id,
            'refresh_rate' => $request->refresh_rate,
            'position' => $request->position,
            'user_id' => $request->user()->id,
        ]);

        if ($request->has('params')){
            foreach($request->params as $name => $value){
                DashboardWidgetParam::create([
                    'dashboard_widget_id' => $dashboardWidget->id,
                    'param_name' => $name,
                    'param_value' => $value,
                ]);
            }
        }
        return response()->json($dashboardWidget->load(['widget.parameters', 'configuredParams']), 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DashboardWidget $dashboardWidget): JsonResponse
    {
        //
        $request->validate([
            'refresh_rate' => 'required|integer|min:10',
            'position' => 'required|integer',
            'params' => 'array',
        ]);

        $dashboardWidget->update($request->only(['refresh_rate', 'position']));
        if ($request->has('params')){
            foreach($request->params as $name => $value){
                DashboardWidgetParam::updateOrCreate(
                    ['dashboard_widget_id' => $dashboardWidget->id, 'param_name' => $name],
                    ['param_value' => $value]
                );
            }
        }

        return response()->json($dashboardWidget->load(['widget.parameters', 'configuredParams']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DashboardWidget $dashboardWidget): JsonResponse
    {
        //
        $dashboardWidget->delete();
        return response()->json([
            'message' => 'Widget removed from dashboard'
        ]);
    }
}
