<?php
namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private function getKit(Request $request, $kitId)
    {
        return $request->user()->kits()->findOrFail($kitId);
    }

    public function index(Request $request, $kitId)
    {
        $kit   = $this->getKit($request, $kitId);
        $items = $kit->items()->with('category')->get();
        return response()->json($items);
    }

    public function store(Request $request, $kitId)
    {
        $kit = $this->getKit($request, $kitId);

        $request->validate([
            'name'        => 'required|string|max:255',
            'quantity'    => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'purchased'   => 'boolean',
        ]);

        $item = $kit->items()->create([
            'name'        => $request->name,
            'quantity'    => $request->quantity ?? 1,
            'category_id' => $request->category_id,
            'purchased'   => $request->purchased ?? false,
        ]);

        return response()->json($item->load('category'), 201);
    }

    public function update(Request $request, $kitId, $id)
    {
        $kit  = $this->getKit($request, $kitId);
        $item = $kit->items()->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'quantity'    => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'purchased'   => 'boolean',
        ]);

        $item->update([
            'name'        => $request->name,
            'quantity'    => $request->quantity ?? 1,
            'category_id' => $request->category_id,
            'purchased'   => $request->purchased ?? false,
        ]);

        return response()->json($item->load('category'));
    }

    public function destroy(Request $request, $kitId, $id)
    {
        $kit  = $this->getKit($request, $kitId);
        $item = $kit->items()->findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Item removido.']);
    }

    public function toggle(Request $request, $kitId, $id)
    {
        $kit  = $this->getKit($request, $kitId);
        $item = $kit->items()->findOrFail($id);
        $item->update(['purchased' => !$item->purchased]);
        return response()->json($item->load('category'));
    }
}
