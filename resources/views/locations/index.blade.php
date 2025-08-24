<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasyon Yönetimi - A101 Stok Sistemi</title>
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
                        <a class="nav-link active" href="/locations">
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
                        <a class="nav-link" href="/stocks">
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
                    <i class="bi bi-geo-alt"></i> Lokasyon Yönetimi
                </h1>
                <p class="text-muted">Hiyerarşik lokasyon yapısını yönetin</p>
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
                                <a href="{{ route('locations.create') }}" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Yeni Lokasyon
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/stocks" class="btn btn-info w-100">
                                    <i class="bi bi-archive"></i> Stok Durumları
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/products" class="btn btn-warning w-100">
                                    <i class="bi bi-box"></i> Ürünler
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/stock-requests/create" class="btn btn-primary w-100">
                                    <i class="bi bi-arrow-left-right"></i> Stok İsteği
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lokasyon Listesi -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-list"></i> Lokasyon Listesi
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($locations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Lokasyon</th>
                                            <th>Tür</th>
                                            <th>Seviye</th>
                                            <th>Üst Lokasyon</th>
                                            <th>Alt Lokasyonlar</th>
                                            <th>Stok Sayısı</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($locations as $location)
                                            <tr>
                                                <td>
                                                    <strong>{{ $location->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($location->description, 50) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $location->type }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">Seviye {{ $location->level }}</span>
                                                </td>
                                                <td>
                                                    @if($location->parent)
                                                        <span class="badge bg-primary">{{ $location->parent->name }}</span>
                                                    @else
                                                        <span class="badge bg-success">Ana Lokasyon</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($location->children_count > 0)
                                                        <span class="badge bg-warning">{{ $location->children_count }} alt lokasyon</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">Alt lokasyon yok</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $location->stocks_count ?? 0 }} stok</span>
                                                </td>
                                                <td>
                                                    @if($location->is_active)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Pasif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('locations.show', $location) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="/stocks?location_id={{ $location->id }}" class="btn btn-sm btn-outline-info">
                                                            <i class="bi bi-archive"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination yok - tüm lokasyonlar tek sayfada gösteriliyor -->
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-geo-alt display-1 text-muted"></i>
                                <h4 class="mt-3">Henüz lokasyon bulunmuyor</h4>
                                <p class="text-muted">İlk lokasyonu oluşturmak için aşağıdaki butona tıklayın.</p>
                                <a href="{{ route('locations.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> İlk Lokasyonu Oluştur
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
