<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Location;

class DashboardController extends Controller
{
    /**
     * Show the user's dashboard
     */
    public function index(): View
    {
        $user = Auth::user();
        $userLocation = $user->location;
        
        if (!$userLocation) {
            return view('dashboard.no-location');
        }

        // Kullanıcının lokasyonundaki stoklar
        $stocks = Stock::with(['product.category'])
            ->where('location_id', $userLocation->id)
            ->get();

        // Düşük stok uyarıları
        $lowStockItems = $stocks->filter(function ($stock) {
            return $stock->isLowStock();
        });

        // Stok yok uyarıları
        $outOfStockItems = $stocks->filter(function ($stock) {
            return $stock->isOutOfStock();
        });

        // Kullanıcının lokasyonuna gelen stok istekleri
        $incomingRequests = StockRequest::with(['product.category', 'fromLocation', 'requestedBy'])
            ->where('to_location_id', $userLocation->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Kullanıcının gönderdiği stok istekleri
        $outgoingRequests = StockRequest::with(['product.category', 'toLocation'])
            ->where('from_location_id', $userLocation->id)
            ->latest()
            ->take(5)
            ->get();

        // Stok istatistikleri
        $totalProducts = $stocks->count();
        $totalQuantity = $stocks->sum('quantity');
        $pendingRequests = StockRequest::where('to_location_id', $userLocation->id)
            ->where('status', 'pending')
            ->count();

        return view('dashboard.index', compact(
            'userLocation',
            'stocks',
            'lowStockItems',
            'outOfStockItems',
            'incomingRequests',
            'outgoingRequests',
            'totalProducts',
            'totalQuantity',
            'pendingRequests'
        ));
    }
}
