<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::with(['parent', 'children', 'products'])
            ->active()
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentCategories = Category::active()->get();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'products']);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::active()->where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // Kendisini parent olarak seçemez
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return redirect()->back()
                ->withErrors(['parent_id' => 'Kategori kendisini üst kategori olarak seçemez.']);
        }

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Alt kategorileri var mı kontrol et
        if ($category->children()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Bu kategorinin alt kategorileri bulunmaktadır. Önce onları silmelisiniz.');
        }

        // Ürünleri var mı kontrol et
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Bu kategoride ürünler bulunmaktadır. Önce ürünleri silmelisiniz.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla silindi.');
    }

    /**
     * Show products in a category
     */
    public function products(Category $category): View
    {
        $products = $category->products()->with(['stocks.location'])->paginate(15);
        return view('categories.products', compact('category', 'products'));
    }
}
