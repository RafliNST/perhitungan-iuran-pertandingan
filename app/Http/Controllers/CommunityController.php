<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreCommunityRequest $request)
    {
        $data = $request->validated();

        $creator = Auth::user();

        $community = Community::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'creator_id' => $creator->id,
        ]);

        return(new CommunityResource($community))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community)
    {
        return new CommunityResource($community);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        $data = $request->validated();

        $community->update($data);

        return new CommunityResource($community);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        $community->delete();

        return response()->noContent();
    }
}
