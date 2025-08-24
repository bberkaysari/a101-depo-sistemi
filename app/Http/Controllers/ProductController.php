<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\CategoryService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['category_id', 'search']);
        $products = $this->productService->paginateProducts($filters, 15);
        $categories = $this->categoryService->getActiveCategories();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = $this->categoryService->getActiveCategories();
        $errors = session('errors') ?? collect();
        return view('products.create', compact('categories', 'errors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $result = $this->productService->createProduct($request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product = $this->productService->getProductById($product->id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = $this->categoryService->getActiveCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $result = $this->productService->updateProduct($product->id, $request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $result = $this->productService->deleteProduct($product->id);

        if (!$result['success']) {
            return redirect()->route('products.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Show stock levels for a product across all locations
     */
    public function stockLevels(Product $product): View
    {
        $stockLevels = $this->productService->getProductStockLevels($product->id);
        return view('products.stock-levels', compact('product', 'stockLevels'));
    }
}
