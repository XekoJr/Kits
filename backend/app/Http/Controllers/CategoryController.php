<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()->categories()->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = $request->user()->categories()->create([
            'name' => $request->name,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = $request->user()->categories()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update(['name' => $request->name]);
        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $category = $request->user()->categories()->findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Categoria removida.']);
    }
}
