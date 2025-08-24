<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $locations = $this->locationService->getAllLocations();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentLocations = $this->locationService->getParentLocations();
        return view('locations.create', compact('parentLocations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,store,branch',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $this->locationService->createLocation($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasyon başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $location = $this->locationService->getLocationById($id);
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $location = $this->locationService->getLocationById($id);
        $parentLocations = $this->locationService->getParentLocations($id);
        return view('locations.edit', compact('location', 'parentLocations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,store,branch',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $this->locationService->updateLocation($id, $validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasyon başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->locationService->deleteLocation($id);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasyon başarıyla silindi.');
    }
}
