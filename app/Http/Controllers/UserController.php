<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) 
        {
            $newFileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $profilePicturePath = $request->file('profile_picture')->storeAs('profile_pictures', $newFileName, 'public');
            $data['profile_url'] = $profilePicturePath;
        }

        $data['password'] = Hash::make($data['password']);
        $data['token'] = Str::uuid()->toString();

        $user = User::create($data);
        
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
