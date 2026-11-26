<?php

namespace App\Http\Controllers;

use App\Enum\ItemDistributionType;
use App\Models\ItemExpense;
use App\Http\Requests\StoreItemExpenseRequest;
use App\Http\Requests\UpdateItemExpenseRequest;
use App\Http\Resources\ItemExpenseResource;
use App\Models\Event;
use App\Models\Item;
use Illuminate\Support\Facades\Gate;

class ItemExpenseController extends Controller
{
    // Menambahkan atau memperbarui jumlah pemakaian item oleh peserta (Hanya Host)
    public function store(StoreItemExpenseRequest $request)
    {
        $data = $request->validated();
        $event = Event::findOrFail($data['event_id']);

        Gate::authorize('manageDetails', $event);

        $item = Item::findOrFail($data['item_id']);

        // Tolak jika item bertipe GLOBAL karena biayanya dibagi rata otomatis
        if ($item->distribution_type === ItemDistributionType::GLOBAL) {
            return response()->json([
                'message' => 'Item bertipe global dibagi rata ke semua peserta dan tidak perlu didata manual.',
            ], 422);
        }

        $expense = ItemExpense::updateOrCreate(
            [
                'item_id'  => $data['item_id'],
                'user_id'  => $data['user_id'],
                'event_id' => $data['event_id'],
            ],
            [
                'qty' => $data['qty'],
            ]
        );

        $expense->load(['user', 'item']);

        return new ItemExpenseResource($expense);
    }

    // Mengubah qty item expense langsung via ID (Hanya Host)
    public function update(UpdateItemExpenseRequest $request, ItemExpense $itemExpense)
    {
        Gate::authorize('manageDetails', $itemExpense->event);

        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $itemExpense->update([
            'qty' => $validated['qty'],
        ]);

        $itemExpense->load(['user', 'item']);

        return new ItemExpenseResource($itemExpense);
    }

    // Menghapus catatan item expense via ID (Hanya Host)
    public function destroy(ItemExpense $itemExpense)
    {
        Gate::authorize('manageDetails', $itemExpense->event);

        $itemExpense->delete();

        return response()->noContent();
    }
}