<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $userLocation->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                A101 Stok Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stocks.index') }}">Stoklarım</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stock-requests.incoming-requests') }}">Gelen İstekler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stock-requests.my-requests') }}">Gönderdiğim İstekler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stock-requests.create') }}">Stok İsteği</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-item-text text-muted">{{ $userLocation->name }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Çıkış Yap</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>{{ $userLocation->name }} Dashboard</h2>
                <p>{{ $userLocation->type }}</p>
                <a href="{{ route('stock-requests.create') }}" class="btn btn-primary">Stok İsteği Oluştur</a>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4>{{ $totalProducts }}</h4>
                        <p>Toplam Ürün</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4>{{ $totalQuantity }}</h4>
                        <p>Toplam Stok</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4>{{ $lowStockItems->count() }}</h4>
                        <p>Düşük Stok</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4>{{ $pendingRequests }}</h4>
                        <p>Bekleyen İstek</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sol Kolon -->
            <div class="col-lg-8">
                <!-- Mevcut Stoklar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Mevcut Stoklarım</h6>
                    </div>
                    <div class="card-body">
                        @if($stocks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Kategori</th>
                                            <th>Miktar</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks->take(8) as $stock)
                                            <tr>
                                                <td>
                                                    <strong>{{ $stock->product->name }}</strong>
                                                    <br><small>{{ $stock->product->sku }}</small>
                                                </td>
                                                <td>{{ $stock->product->category->name }}</td>
                                                <td>{{ $stock->quantity }} {{ $stock->product->unit }}</td>
                                                <td>
                                                    @if($stock->isOutOfStock())
                                                        <span class="badge bg-danger">Stok Yok</span>
                                                    @elseif($stock->isLowStock())
                                                        <span class="badge bg-warning">Düşük Stok</span>
                                                    @else
                                                        <span class="badge bg-success">Normal</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-primary">Tüm Stokları Görüntüle</a>
                            </div>
                        @else
                            <p class="text-center text-muted">Henüz stok bulunmuyor</p>
                        @endif
                    </div>
                </div>

                <!-- Düşük Stok Uyarıları -->
                @if($lowStockItems->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6>Düşük Stok Uyarıları</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Mevcut</th>
                                            <th>Minimum</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockItems->take(5) as $stock)
                                            <tr>
                                                <td>
                                                    <strong>{{ $stock->product->name }}</strong>
                                                    <br><small>{{ $stock->product->category->name }}</small>
                                                </td>
                                                <td>{{ $stock->quantity }} {{ $stock->product->unit }}</td>
                                                <td>{{ $stock->min_quantity }} {{ $stock->product->unit }}</td>
                                                <td>
                                                    <a href="{{ route('stock-requests.create') }}" class="btn btn-sm btn-warning">Stok İste</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('stocks.low-stock') }}" class="btn btn-sm btn-warning">Tümünü Görüntüle</a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Stok Yok Uyarıları -->
                @if($outOfStockItems->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6>Stok Yok Uyarıları</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Kategori</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($outOfStockItems->take(5) as $stock)
                                            <tr>
                                                <td>
                                                    <strong>{{ $stock->product->name }}</strong>
                                                    <br><small>{{ $stock->product->sku }}</small>
                                                </td>
                                                <td>{{ $stock->product->category->name }}</td>
                                                <td>
                                                    <a href="{{ route('stock-requests.create') }}" class="btn btn-sm btn-danger">Stok İste</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('stocks.out-of-stock') }}" class="btn btn-sm btn-danger">Tümünü Görüntüle</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sağ Kolon -->
            <div class="col-lg-4">
                <!-- Gelen Stok İstekleri -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Gelen Stok İstekleri</h6>
                    </div>
                    <div class="card-body">
                        @if($incomingRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Gönderen</th>
                                            <th>Miktar</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($incomingRequests as $request)
                                            <tr>
                                                <td>
                                                    <strong>{{ $request->product->name }}</strong>
                                                    <br><small>{{ $request->product->category->name }}</small>
                                                </td>
                                                <td>{{ $request->fromLocation->name }}</td>
                                                <td>{{ $request->requested_quantity }} {{ $request->product->unit }}</td>
                                                <td>
                                                    <a href="{{ route('stock-requests.show', $request) }}" class="btn btn-sm btn-primary">Görüntüle</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('stock-requests.incoming-requests') }}" class="btn btn-sm btn-primary">Tümünü Görüntüle</a>
                            </div>
                        @else
                            <p class="text-center text-muted">Gelen stok isteği bulunmuyor</p>
                        @endif
                    </div>
                </div>

                <!-- Gönderdiğim İstekler -->
                <div class="card">
                    <div class="card-header">
                        <h6>Gönderdiğim İstekler</h6>
                    </div>
                    <div class="card-body">
                        @if($outgoingRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Alıcı</th>
                                            <th>Miktar</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($outgoingRequests as $request)
                                            <tr>
                                                <td>
                                                    <strong>{{ $request->product->name }}</strong>
                                                    <br><small>{{ $request->product->category->name }}</small>
                                                </td>
                                                <td>{{ $request->toLocation->name }}</td>
                                                <td>{{ $request->requested_quantity }} {{ $request->product->unit }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                        <span class="badge bg-warning">Beklemede</span>
                                                    @elseif($request->status == 'approved')
                                                        <span class="badge bg-success">Onaylandı</span>
                                                    @elseif($request->status == 'rejected')
                                                        <span class="badge bg-danger">Reddedildi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('stock-requests.my-requests') }}" class="btn btn-sm btn-info">Tümünü Görüntüle</a>
                            </div>
                        @else
                            <p class="text-center text-muted">Gönderdiğiniz istek bulunmuyor</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
