<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get public communites
        $communities = Community::with('owner')
            ->where('is_public', true)
            ->latest()
            ->get();
        
        return (CommunityResource::collection($communities));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        $data = $request->validated();

        $creator = Auth::user();

        $data['slug'] = Str::slug($data['name']) . '-' . $creator->name;
        $data['owner_id'] = $creator->id;

        if ($request->hasFile('community_banner')) {
            $newFileName = time() . '-' . $creator->name . '.' . $request->file('community_banner')->getClientOriginalExtension();
            $bannerPath = $request->file('community_banner')->storeAs('community_banners', $newFileName, 'public');
            $data['banner_url'] = $bannerPath;
        }

        $community = Community::create($data);

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
