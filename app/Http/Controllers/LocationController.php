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
        $result = $this->locationService->createLocation($request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

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
        $result = $this->locationService->updateLocation($id, $request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('locations.index')
            ->with('success', 'Lokasyon başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $result = $this->locationService->deleteLocation($id);

        if (!$result['success']) {
            return redirect()->route('locations.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('locations.index')
            ->with('success', 'Lokasyon başarıyla silindi.');
    }
}
