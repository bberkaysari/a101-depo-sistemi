<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getActiveCategories();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentCategories = $this->categoryService->getParentCategories();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $result = $this->categoryService->createCategory($request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category = $this->categoryService->getCategoryById($category->id);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $parentCategories = $this->categoryService->getParentCategories();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $result = $this->categoryService->updateCategory($category->id, $request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $result = $this->categoryService->deleteCategory($category->id);

        if (!$result['success']) {
            return redirect()->route('categories.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla silindi.');
    }

    /**
     * Show products in a category
     */
    public function products(Category $category): View
    {
        $products = $this->categoryService->getProductsByCategory($category->id);
        return view('categories.products', compact('category', 'products'));
    }
}
