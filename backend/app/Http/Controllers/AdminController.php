<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Service;
use App\Models\Widget;
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //
        $users = User::all();
        return response()->json($users);
        
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function updateUser(Request $request, User $user): JsonResponse
    {
        //
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);
        
        $user->update(['role' => $request->role]);
        return response()->json($user);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyUser(User $user): JsonResponse
    {
        //
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

   
}