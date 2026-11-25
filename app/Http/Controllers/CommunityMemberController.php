<?php

namespace App\Http\Controllers;

use App\Models\CommunityMember;
use App\Http\Requests\StoreCommunityMemberRequest;
use App\Http\Requests\UpdateCommunityMemberRequest;
use App\Http\Resources\CommunityMemberResource;
use App\Models\Community;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommunityMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityMemberRequest $request)
    {
        $data = $request->validated();
        $community = Community::findOrFail($data['community_id']);
        
        $targetUserId = $data['user_id'] ?? Auth::id();

        // Kirim $targetUserId ke CommunityPolicy@join
        Gate::authorize('join', [$community, $targetUserId]);

        $community_member = CommunityMember::create([
            'community_id' => $community->id,
            'user_id'      => $targetUserId,
        ]);

        $community_member->load(['user', 'community']);

        return new CommunityMemberResource($community_member);
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityMember $communityMember)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityMember $communityMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityMemberRequest $request, CommunityMember $communityMember)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityMember $communityMember)
    {
        // Otomatis melempar 403 Forbidden jika bukan member ybs atau owner komunitas
        Gate::authorize('delete', $communityMember);

        $communityMember->delete();

        return response()->noContent(); // 204 No Content
    }

    public function destroyByUser(Community $community, User $user)
    {
        $communityMember = CommunityMember::where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        Gate::authorize('delete', $communityMember);

        $communityMember->delete();

        return response()->noContent();
    }
}
