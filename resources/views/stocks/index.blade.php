<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Yönetimi - A101 Stok Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-box-seam"></i> A101 Stok Yönetim Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/locations">
                            <i class="bi bi-geo-alt"></i> Lokasyonlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories">
                            <i class="bi bi-tags"></i> Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">
                            <i class="bi bi-box"></i> Ürünler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/stocks">
                            <i class="bi bi-archive"></i> Stoklar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/stock-requests">
                            <i class="bi bi-arrow-left-right"></i> Stok İstekleri
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6">
                    <i class="bi bi-archive"></i> Stok Yönetimi
                </h1>
                <p class="text-muted">Tüm lokasyonlardaki stok durumlarını görüntüleyin ve yönetin</p>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-funnel"></i> Filtreler
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('stocks.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="location_id" class="form-label">Lokasyon</label>
                                <select name="location_id" id="location_id" class="form-select">
                                    <option value="">Tüm Lokasyonlar</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="product_id" class="form-label">Ürün</label>
                                <select name="product_id" id="product_id" class="form-select">
                                    <option value="">Tüm Ürünler</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Durum</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Düşük Stok</option>
                                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Stokta Yok</option>
                                    <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>Normal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Filtrele
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning"></i> Hızlı Erişim
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stocks.create') }}" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Yeni Stok Ekle
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stocks.low-stock') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-exclamation-triangle"></i> Düşük Stoklar
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stocks.out-of-stock') }}" class="btn btn-danger w-100">
                                    <i class="bi bi-x-circle"></i> Stokta Olmayanlar
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/stock-requests/create" class="btn btn-info w-100">
                                    <i class="bi bi-arrow-left-right"></i> Stok İsteği
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Listesi -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-list"></i> Stok Listesi
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($stocks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Kategori</th>
                                            <th>Lokasyon</th>
                                            <th>Miktar</th>
                                            <th>Min. Miktar</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks as $stock)
                                            <tr>
                                                <td>
                                                    <strong>{{ $stock->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $stock->product->sku }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $stock->product->category->name }}</span>
                                                </td>
                                                <td>
                                                    <i class="bi bi-geo-alt text-primary"></i>
                                                    {{ $stock->location->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $stock->location->type }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $stock->quantity > 0 ? 'success' : 'danger' }}">
                                                        {{ $stock->quantity }} {{ $stock->product->unit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $stock->min_quantity }} {{ $stock->product->unit }}</span>
                                                </td>
                                                <td>
                                                    @if($stock->isOutOfStock())
                                                        <span class="badge bg-danger">Stokta Yok</span>
                                                    @elseif($stock->isLowStock())
                                                        <span class="badge bg-warning">Düşük Stok</span>
                                                    @else
                                                        <span class="badge bg-success">Normal</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#quantityModal{{ $stock->id }}">
                                                            <i class="bi bi-plus-circle"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Miktar Güncelleme Modal -->
                                            <div class="modal fade" id="quantityModal{{ $stock->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Stok Miktarı Güncelle</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('stocks.update-quantity', $stock) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Ürün: <strong>{{ $stock->product->name }}</strong></label>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Lokasyon: <strong>{{ $stock->location->name }}</strong></label>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="operation" class="form-label">İşlem</label>
                                                                    <select name="operation" id="operation" class="form-select" required>
                                                                        <option value="set">Miktar Belirle</option>
                                                                        <option value="add">Miktar Ekle</option>
                                                                        <option value="remove">Miktar Çıkar</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="quantity" class="form-label">Miktar</label>
                                                                    <input type="number" name="quantity" id="quantity" class="form-control" min="0" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-primary">Güncelle</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination yok - tüm stoklar tek sayfada gösteriliyor -->
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-archive display-1 text-muted"></i>
                                <h4 class="mt-3">Henüz stok kaydı bulunmuyor</h4>
                                <p class="text-muted">İlk stok kaydını oluşturmak için aşağıdaki butona tıklayın.</p>
                                <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> İlk Stok Kaydını Oluştur
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
