<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['show', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        if ($request->user()->id != $user->id) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($request->user()->id != $user->id) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $user->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'password' => 'sometimes|min:8|max:255'
            ])
        );

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id != $user->id) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $user->delete();

        return response(status: 204);
    }
}
