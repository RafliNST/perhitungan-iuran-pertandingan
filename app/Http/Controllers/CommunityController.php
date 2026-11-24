<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $creator = $request->user();

        // Gunakan Str::slug agar URL bersih dari spasi/huruf kapital & tetap unik
        $data['slug'] = Str::slug($data['name'] . '-' . $creator->name);
        $data['owner_id'] = $creator->id;

        if ($request->hasFile('community_banner')) {
            $banner_url = $request->file('community_banner')->store('community_banners', 'public');    
            $data['banner_url'] = $banner_url;
        }

        unset($data['community_banner']);

        $community = Community::create($data);
        $community->load('owner');

        return (new CommunityResource($community))
            ->response()
            ->setStatusCode(201);
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
        $creator = Auth::user();

        if ($request->hasFile('community_banner')) {
            if ($community->banner_url && Storage::disk('public')->exists($community->banner_url)) {
                Storage::disk('public')->delete($community->banner_url);
            }

            $data['banner_url'] = $request->file('community_banner')->store('community_banners', 'public');
        }

        unset($data['community_banner']);
        
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name'] . '-' . $creator->name);
        }

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
