<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
class AboutController extends Controller
{
    //
    public function index(Request $request): JsonResponse
    {
        $services = Service::with('widgets.parameters')->get();
        
        return response()->json([
            'customer' => [
                'host' => $request->ip(),
            ],
            'server' => [
                'current_time' => now()->timestamp,
                'services' => $services,
            ],
        ]);
    }
}