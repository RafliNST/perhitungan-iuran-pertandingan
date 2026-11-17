<?php

namespace App\Http\Controllers;

use App\Models\ItemExpense;
use App\Http\Requests\StoreItemExpenseRequest;
use App\Http\Requests\UpdateItemExpenseRequest;

class ItemExpenseController extends Controller
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
    public function store(StoreItemExpenseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemExpense $itemExpense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemExpense $itemExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemExpenseRequest $request, ItemExpense $itemExpense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemExpense $itemExpense)
    {
        //
    }
}
