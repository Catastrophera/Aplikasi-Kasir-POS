<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:categories,name|max:60']);
        Category::create([
            'name' => $request->name,
            'sort_order' => Category::max('sort_order') + 1,
        ]);
        return redirect('/menus')->with('success', 'Kategori "' . $request->name . '" berhasil ditambahkan.');
    }

    public function destroy(Category $category)
    {
        // Cek apakah masih ada menu yang menggunakan kategori ini
        if ($category->menus()->count() > 0) {
            return redirect('/menus')->with('error', 'Kategori "' . $category->name . '" tidak bisa dihapus karena masih digunakan oleh ' . $category->menus()->count() . ' menu.');
        }
        $category->delete();
        return redirect('/menus')->with('success', 'Kategori "' . $category->name . '" berhasil dihapus.');
    }
}
