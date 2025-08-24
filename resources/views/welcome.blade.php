<!DOCTYPE html>
<html lang="tr">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A101 Stok Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
            <style>
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
            </style>
    </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                A101 Stok Yönetim Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('locations.index') }}">Lokasyonlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Kategoriler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Ürünler</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stocks.index') }}">Stoklar</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stock-requests.index') }}">Stok İstekleri</a>
                        </li>
                    </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 text-center mb-4">
                    A101 Stok Yönetim Sistemi
                </h1>
                <p class="text-center text-muted">
                    Hiyerarşik depo ve stok yönetim sistemi
                </p>
                <div class="text-center mb-4">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        Mağaza Girişi Yap
                    </a>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card card-hover">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-2">Lokasyonlar</h5>
                        <h3 class="card-text">{{ \App\Models\Location::count() }}</h3>
                        <small>Depo, Mağaza, Şube</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card success card-hover">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-2">Ürünler</h5>
                        <h3 class="card-text">{{ \App\Models\Product::count() }}</h3>
                        <small>Toplam Ürün</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card warning card-hover">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-2">Stok Girişleri</h5>
                        <h3 class="card-text">{{ \App\Models\Stock::count() }}</h3>
                        <small>Lokasyon Bazlı</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card info card-hover">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-2">Aktif İstekler</h5>
                        <h3 class="card-text">{{ \App\Models\StockRequest::where('status', 'pending')->count() }}</h3>
                        <small>Bekleyen İstekler</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">
                    Hızlı Erişim
                </h4>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-3">Yeni Ürün Ekle</h5>
                        <p class="card-text">Sisteme yeni ürün ekleyin ve kategorilere ayırın.</p>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            Ürün Ekle
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-3">Stok Yönetimi</h5>
                        <p class="card-text">Stok miktarlarını görüntüleyin ve güncelleyin.</p>
                        <a href="{{ route('stocks.index') }}" class="btn btn-success">
                            Stokları Görüntüle
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">

                        <h5 class="card-title mt-3">Stok İsteği</h5>
                        <p class="card-text">Diğer lokasyonlardan stok isteği oluşturun.</p>
                        <a href="{{ route('stock-requests.create') }}" class="btn btn-warning">
                            İstek Oluştur
                        </a>
                    </div>
                </div>
            </div>
        </div>



        <!-- Footer -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <hr>
                <p class="text-muted">
                    A101 Stok Yönetim Sistemi - Laravel ile geliştirilmiştir
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
