<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'stocks.location']);

        // Filtreleme
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->active()->paginate(15);
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        $errors = session('errors') ?? collect();
        return view('products.create', compact('categories', 'errors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100|unique:products',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'stocks.location', 'stockRequests']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Ürünün stokları var mı kontrol et
        if ($product->stocks()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Bu ürünün stokları bulunmaktadır. Önce stokları silmelisiniz.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Show stock levels for a product across all locations
     */
    public function stockLevels(Product $product): View
    {
        $stocks = $product->stocks()->with('location')->get();
        $locations = \App\Models\Location::active()->get();
        
        // Lokasyonlarda stok yoksa 0 olarak göster
        $stockLevels = $locations->map(function($location) use ($stocks) {
            $stock = $stocks->where('location_id', $location->id)->first();
            return [
                'location' => $location,
                'quantity' => $stock ? $stock->quantity : 0,
                'min_quantity' => $stock ? $stock->min_quantity : 0,
                'max_quantity' => $stock ? $stock->max_quantity : null,
                'stock' => $stock
            ];
        });

        return view('products.stock-levels', compact('product', 'stockLevels'));
    }
}
