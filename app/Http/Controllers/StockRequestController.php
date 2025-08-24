<?php

namespace App\Http\Controllers;

use App\Services\StockRequestService;
use App\Services\ProductService;
use App\Services\LocationService;
use App\Models\StockRequest;
use App\Models\Product;
use App\Models\Location;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StockRequestController extends Controller
{
    protected $stockRequestService;
    protected $productService;
    protected $locationService;

    public function __construct(StockRequestService $stockRequestService, ProductService $productService, LocationService $locationService)
    {
        $this->stockRequestService = $stockRequestService;
        $this->productService = $productService;
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'from_location_id', 'to_location_id', 'product_id']);
        $filters['per_page'] = 20;
        $stockRequests = $this->stockRequestService->getRequestsWithFilters($filters);
        $locations = $this->locationService->getActiveLocations();
        $products = $this->productService->getActiveProducts();

        return view('stock-requests.index', compact('stockRequests', 'locations', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::active()->get();
        $currentUserLocation = Auth::user()->location_id;
        
        // Kullanıcının kendi lokasyonu hariç diğer tüm lokasyonları göster
        $locations = Location::active()->where('id', '!=', $currentUserLocation)->get();
        $errors = session('errors') ?? collect();
        
        return view('stock-requests.create', compact('products', 'locations', 'errors', 'currentUserLocation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id',
            'requested_quantity' => 'required|integer|min:1',
            'request_notes' => 'nullable|string',
        ]);

        // Kullanıcının kendi lokasyonundan başka bir lokasyona istek yapabilmesi için
        // Gönderen lokasyon olarak kullanıcının lokasyonu seçilemez
        $currentUserLocation = Auth::user()->location_id;
        
        // Eğer kullanıcı kendi lokasyonundan istek yapmaya çalışıyorsa, hata ver
        if ($validated['from_location_id'] == $currentUserLocation) {
            return redirect()->back()
                ->withErrors(['from_location_id' => 'Kendi lokasyonunuzdan stok isteği yapamazsınız.']);
        }
        
        // Alıcı lokasyon olarak kullanıcının kendi lokasyonu otomatik seçilir
        $validated['to_location_id'] = $currentUserLocation;

        // Aynı lokasyondan aynı lokasyona istek yapılamaz
        if ($validated['from_location_id'] == $validated['to_location_id']) {
            return redirect()->back()
                ->withErrors(['from_location_id' => 'Aynı lokasyondan aynı lokasyona stok isteği yapılamaz.']);
        }

        // Gönderen lokasyonda yeterli stok var mı kontrol et
        $fromStock = Stock::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['from_location_id'])
            ->first();

        if (!$fromStock || $fromStock->quantity < $validated['requested_quantity']) {
            return redirect()->back()
                ->withErrors(['requested_quantity' => 'Gönderen lokasyonda yeterli stok bulunmamaktadır.']);
        }

        // Giriş yapan kullanıcının ID'sini kullan
        $validated['requested_by'] = Auth::id();

        StockRequest::create($validated);

        return redirect()->route('stock-requests.index')
            ->with('success', 'Stok isteği başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockRequest $stockRequest): View
    {
        // Relationship'leri manuel olarak yükle
        $stockRequest = StockRequest::with(['product.category', 'fromLocation', 'toLocation', 'requestedBy', 'respondedBy'])
            ->findOrFail($stockRequest->id);
            
        return view('stock-requests.show', compact('stockRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockRequest $stockRequest): View
    {
        // Sadece bekleyen istekler düzenlenebilir
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.index')
                ->with('error', 'Sadece bekleyen istekler düzenlenebilir.');
        }

        $products = Product::active()->get();
        $locations = Location::active()->get();
        return view('stock-requests.edit', compact('stockRequest', 'products', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockRequest $stockRequest): RedirectResponse
    {
        // Sadece bekleyen istekler güncellenebilir
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.index')
                ->with('error', 'Sadece bekleyen istekler güncellenebilir.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id',
            'requested_quantity' => 'required|integer|min:1',
            'request_notes' => 'nullable|string',
        ]);

        // Aynı lokasyondan aynı lokasyona istek yapılamaz
        if ($validated['from_location_id'] == $validated['to_location_id']) {
            return redirect()->back()
                ->withErrors(['to_location_id' => 'Aynı lokasyondan aynı lokasyona stok isteği yapılamaz.']);
        }

        $stockRequest->update($validated);

        return redirect()->route('stock-requests.index')
            ->with('success', 'Stok isteği başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockRequest $stockRequest): RedirectResponse
    {
        // Sadece bekleyen istekler silinebilir
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.index')
                ->with('error', 'Sadece bekleyen istekler silinebilir.');
        }

        // Sadece isteği oluşturan mağaza silebilir
        if ($stockRequest->from_location_id != 4) { // Hürriyet Şubesi (ID: 4) - mülakat için sabit
            return redirect()->route('stock-requests.index')
                ->with('error', 'Bu isteği silme yetkiniz bulunmamaktadır.');
        }

        $stockRequest->delete();

        return redirect()->route('stock-requests.index')
            ->with('success', 'Stok isteği başarıyla silindi.');
    }

    /**
     * Approve a stock request
     */
    public function approve(Request $request, StockRequest $stockRequest): RedirectResponse
    {
        // Debug log
        \Log::info('Approve attempt', [
            'request_id' => $stockRequest->id,
            'status' => $stockRequest->status,
            'from_location_id' => $stockRequest->from_location_id,
            'to_location_id' => $stockRequest->to_location_id,
            'is_pending' => $stockRequest->isPending()
        ]);

        // Sadece bekleyen istekler onaylanabilir
        if (!$stockRequest->isPending()) {
            \Log::warning('Request not pending', ['request_id' => $stockRequest->id]);
            return redirect()->back()
                ->with('error', 'Bu istek zaten onaylanmış veya reddedilmiş.');
        }

        // Sadece isteği alan mağaza onaylayabilir
        $currentUserLocation = Auth::user()->location_id;
        
        // 1. Kullanıcı sadece kendi lokasyonuna gelen istekleri onaylayabilir
        if ($stockRequest->to_location_id != $currentUserLocation) {
            \Log::warning('Unauthorized approval attempt - wrong recipient', [
                'request_id' => $stockRequest->id,
                'to_location_id' => $stockRequest->to_location_id,
                'current_user_location' => $currentUserLocation
            ]);
            return redirect()->back()
                ->with('error', 'Bu isteği onaylama yetkiniz bulunmamaktadır. Sadece kendi lokasyonunuza gelen istekleri onaylayabilirsiniz.');
        }
        
        // 2. Gönderen lokasyon kendi isteğini onaylayamaz
        if ($stockRequest->from_location_id == $currentUserLocation) {
            \Log::warning('Unauthorized approval attempt - sender trying to approve own request', [
                'request_id' => $stockRequest->id,
                'from_location_id' => $stockRequest->from_location_id,
                'current_user_location' => $currentUserLocation
            ]);
            return redirect()->back()
                ->with('error', 'Kendi gönderdiğiniz isteği onaylayamazsınız. Sadece size gelen istekleri onaylayabilirsiniz.');
        }

        $validated = $request->validate([
            'approved_quantity' => 'required|integer|min:1|max:' . $stockRequest->requested_quantity,
            'response_notes' => 'nullable|string',
        ]);

        // Gönderen lokasyonda yeterli stok var mı kontrol et
        $fromStock = \App\Models\Stock::where('product_id', $stockRequest->product_id)
            ->where('location_id', $stockRequest->from_location_id)
            ->first();

        if (!$fromStock || $fromStock->quantity < $validated['approved_quantity']) {
            return redirect()->back()
                ->with('error', 'Gönderen lokasyonda yeterli stok bulunmamaktadır.');
        }

        $stockRequest->approve(
            $validated['approved_quantity'],
            $validated['response_notes'],
            Auth::id() // Giriş yapan kullanıcı
        );

        return redirect()->back()
            ->with('success', 'Stok isteği başarıyla onaylandı ve transfer gerçekleştirildi.');
    }

    /**
     * Reject a stock request
     */
    public function reject(Request $request, StockRequest $stockRequest): RedirectResponse
    {
        // Sadece bekleyen istekler reddedilebilir
        if (!$stockRequest->isPending()) {
            return redirect()->back()
                ->with('error', 'Bu istek zaten onaylanmış veya reddedilmiş.');
        }

        // Sadece isteği alan mağaza reddedebilir
        $currentUserLocation = Auth::user()->location_id;
        
        // 1. Kullanıcı sadece kendi lokasyonuna gelen istekleri reddedebilir
        if ($stockRequest->to_location_id != $currentUserLocation) {
            return redirect()->back()
                ->with('error', 'Bu isteği reddetme yetkiniz bulunmamaktadır. Sadece kendi lokasyonunuza gelen istekleri reddedebilirsiniz.');
        }
        
        // 2. Gönderen lokasyon kendi isteğini reddedemez
        if ($stockRequest->from_location_id == $currentUserLocation) {
            return redirect()->back()
                ->with('error', 'Kendi gönderdiğiniz isteği reddedemezsiniz. Sadece size gelen istekleri reddedebilirsiniz.');
        }

        $validated = $request->validate([
            'response_notes' => 'required|string',
        ]);

        $stockRequest->reject(
            $validated['response_notes'],
            Auth::id() // Giriş yapan kullanıcı
        );

        return redirect()->back()
            ->with('success', 'Stok isteği reddedildi.');
    }

    /**
     * Show my requests
     */
    public function myRequests(): View
    {
        // Giriş yapan kullanıcının isteklerini getir
        $stockRequests = StockRequest::with(['product.category', 'fromLocation', 'toLocation'])
            ->where('requested_by', Auth::id())
            ->latest()
            ->get(); // Pagination yerine tüm sonuçları getir

        return view('stock-requests.my-requests', compact('stockRequests'));
    }

    /**
     * Show requests to my location
     */
    public function incomingRequests(): View
    {
        // Giriş yapan kullanıcının lokasyonuna gelen istekleri getir
        $currentUser = Auth::user();
        $currentUserLocation = $currentUser->location_id;
        
        $stockRequests = StockRequest::with(['product.category', 'fromLocation', 'requestedBy'])
            ->where('to_location_id', $currentUserLocation)
            ->latest()
            ->get(); // Pagination yerine tüm sonuçları getir

        return view('stock-requests.incoming-requests', compact('stockRequests'));
    }
}
