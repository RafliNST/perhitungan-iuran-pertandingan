<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Auth::user();
        return UserResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreUserRequest $request)
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

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return (new UserResource($user))
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'email atau kata sandi salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(200);
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
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user = Auth::user();

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) 
        {
            // Delete the old profile picture if it exists
            if ($user->profile_url) {
                $oldProfilePicturePath = public_path('storage/' . $user->profile_url);
                if (file_exists($oldProfilePicturePath)) {
                    unlink($oldProfilePicturePath);
                }
            }

            $newFileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $profilePicturePath = $request->file('profile_picture')->storeAs('profile_pictures', $newFileName, 'public');
            $data['profile_url'] = $profilePicturePath;
        }

        // Update the user with the validated data
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        // Update the user's password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function logout()
    {
        // $user = Auth::user();
        // $user->token = null;
        // $user->save();

        // return response()->json(['message' => 'Logout successful'], 200);
    }
}
