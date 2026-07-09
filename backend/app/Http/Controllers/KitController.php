<?php
namespace App\Http\Controllers;

use App\Models\Kit;
use Illuminate\Http\Request;

class KitController extends Controller
{
    public function index(Request $request)
    {
        $kits = $request->user()->kits()->withCount('items')->get();
        return response()->json($kits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $kit = $request->user()->kits()->create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($kit, 201);
    }

    public function show(Request $request, $id)
    {
        $kit = $request->user()->kits()->with(['items.category'])->findOrFail($id);
        return response()->json($kit);
    }

    public function update(Request $request, $id)
    {
        $kit = $request->user()->kits()->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $kit->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($kit);
    }

    public function destroy(Request $request, $id)
    {
        $kit = $request->user()->kits()->findOrFail($id);
        $kit->delete();
        return response()->json(['message' => 'Kit removido.']);
    }
}
