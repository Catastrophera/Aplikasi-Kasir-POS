<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::orderBy('name')->get();
        // Load menus with their categories and current recipe mappings
        $menus = Menu::with(['category', 'recipes.rawMaterial'])->get()->sortBy(fn($m) => $m->category?->sort_order ?? 99);
        return view('management.stock', compact('rawMaterials', 'menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:raw_materials,name',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'min_stock' => 'required|numeric|min:0',
        ]);

        RawMaterial::create($request->all());

        return back()->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:raw_materials,name,' . $rawMaterial->id,
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'min_stock' => 'required|numeric|min:0',
        ]);

        $rawMaterial->update($request->all());

        return back()->with('success', 'Bahan baku berhasil diperbarui!');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return back()->with('success', 'Bahan baku berhasil dihapus!');
    }

    // Recipe Management
    public function storeRecipe(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        Recipe::updateOrCreate(
            [
                'menu_id' => $request->menu_id,
                'raw_material_id' => $request->raw_material_id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        return back()->with('success', 'Resep menu berhasil disimpan!');
    }

    public function destroyRecipe(Recipe $recipe)
    {
        $recipe->delete();
        return back()->with('success', 'Bahan berhasil dihapus dari resep menu!');
    }

    // AJAX Endpoint for Notifications
    public function lowStockAlerts()
    {
        $lowStockItems = RawMaterial::whereRaw('stock <= min_stock')->get();
        return response()->json([
            'count' => $lowStockItems->count(),
            'items' => $lowStockItems,
        ]);
    }
}
