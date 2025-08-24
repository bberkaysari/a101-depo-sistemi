<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Stock::with(['product.category', 'location']);

        // Filtreleme
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->outOfStock();
                    break;
                case 'normal':
                    $query->inStock()->whereRaw('quantity > min_quantity');
                    break;
            }
        }

        $stocks = $query->paginate(20);
        $locations = Location::active()->get();
        $products = Product::active()->get();

        return view('stocks.index', compact('stocks', 'locations', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::active()->get();
        $locations = Location::active()->get();
        return view('stocks.create', compact('products', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Aynı ürün-lokasyon kombinasyonunda stok var mı kontrol et
        $existingStock = Stock::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id'])
            ->first();

        if ($existingStock) {
            return redirect()->back()
                ->withErrors(['product_id' => 'Bu ürün için bu lokasyonda zaten stok tanımlanmış.']);
        }

        Stock::create($validated);

        return redirect()->route('stocks.index')
            ->with('success', 'Stok başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock): View
    {
        $stock->load(['product.category', 'location']);
        return view('stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock): View
    {
        $products = Product::active()->get();
        $locations = Location::active()->get();
        return view('stocks.edit', compact('stock', 'products', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Aynı ürün-lokasyon kombinasyonunda başka stok var mı kontrol et
        $existingStock = Stock::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id'])
            ->where('id', '!=', $stock->id)
            ->first();

        if ($existingStock) {
            return redirect()->back()
                ->withErrors(['product_id' => 'Bu ürün için bu lokasyonda zaten stok tanımlanmış.']);
        }

        $stock->update($validated);

        return redirect()->route('stocks.index')
            ->with('success', 'Stok başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->delete();

        return redirect()->route('stocks.index')
            ->with('success', 'Stok başarıyla silindi.');
    }

    /**
     * Update stock quantity
     */
    public function updateQuantity(Request $request, Stock $stock): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,remove',
        ]);

        switch ($validated['operation']) {
            case 'set':
                $stock->setStock($validated['quantity']);
                break;
            case 'add':
                $stock->addStock($validated['quantity']);
                break;
            case 'remove':
                if (!$stock->removeStock($validated['quantity'])) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'Yeterli stok bulunmamaktadır.']);
                }
                break;
        }

        return redirect()->back()
            ->with('success', 'Stok miktarı başarıyla güncellendi.');
    }

    /**
     * Show low stock items
     */
    public function lowStock(): View
    {
        $stocks = Stock::with(['product.category', 'location'])
            ->lowStock()
            ->paginate(20);

        return view('stocks.low-stock', compact('stocks'));
    }

    /**
     * Show out of stock items
     */
    public function outOfStock(): View
    {
        $stocks = Stock::with(['product.category', 'location'])
            ->outOfStock()
            ->paginate(20);

        return view('stocks.out-of-stock', compact('stocks'));
    }
}
