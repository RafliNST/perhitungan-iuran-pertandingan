<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\CommunityResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $user->load(['communities', 'events', 'communityMemberships', 'eventsParticipations']);

        return response()->json([
            'user' => new UserResource($user),
            'communities' => CommunityResource::collection($user->communities),
            'events' => EventResource::collection($user->events),
            // 'membership' => MembershipResource::collection($user->communityMemberships),
            // 'participated_events' => EventResource::collection($user->eventsParticipations),
        ], 200);
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
        
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama dari disk public jika ada
            if ($user->profile_url && Storage::disk('public')->exists($user->profile_url)) {
                Storage::disk('public')->delete($user->profile_url);
            }

            $data['profile_url'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Hapus key 'profile_picture' agar tidak ikut masuk ke query SQL
        unset($data['profile_picture']);

        // Hash password baru jika dikirim
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

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
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Berhasil logout'
        ], 200);
    }
}
