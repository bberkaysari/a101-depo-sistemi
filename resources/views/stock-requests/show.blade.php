<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok İsteği Detayı - A101 Stok Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
                                <a class="navbar-brand" href="/">A101 Stok Yönetim Sistemi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/locations">Lokasyonlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories">Kategoriler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Ürünler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/stocks">Stoklar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/stock-requests">Stok İstekleri</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Stok İsteği Detayı</h2>
                <p class="text-muted">Stok isteği hakkında detaylı bilgileri görüntüleyin</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- İstek Detayları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>İstek Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ürün:</label>
                                <p>{{ $stockRequest->product->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">SKU:</label>
                                <p><code>{{ $stockRequest->product->sku }}</code></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kategori:</label>
                                <p><span class="badge bg-secondary">{{ $stockRequest->product->category->name }}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Birim:</label>
                                <p>{{ $stockRequest->product->unit }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">İstenen Miktar:</label>
                                <p><span class="badge bg-info">{{ $stockRequest->requested_quantity }} {{ $stockRequest->product->unit }}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Durum:</label>
                                @if($stockRequest->status == 'pending')
                                    <span class="badge bg-warning">Beklemede</span>
                                @elseif($stockRequest->status == 'approved')
                                    <span class="badge bg-success">Onaylandı</span>
                                @elseif($stockRequest->status == 'rejected')
                                    <span class="badge bg-danger">Reddedildi</span>
                                @elseif($stockRequest->status == 'completed')
                                    <span class="badge bg-primary">Tamamlandı</span>
                                @endif
                            </div>
                        </div>

                        @if($stockRequest->request_notes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">İstek Notları:</label>
                                <p>{{ $stockRequest->request_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lokasyon Bilgileri -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Lokasyon Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Gönderen Lokasyon:</label>
                                <p>
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    {{ $stockRequest->fromLocation->name }}
                                </p>
                                <small class="text-muted">{{ $stockRequest->fromLocation->type }} - Seviye {{ $stockRequest->fromLocation->level }}</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Alıcı Lokasyon:</label>
                                <p>
                                    <i class="bi bi-geo-alt text-success"></i>
                                    {{ $stockRequest->toLocation->name }}
                                </p>
                                <small class="text-muted">{{ $stockRequest->toLocation->type }} - Seviye {{ $stockRequest->toLocation->level }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Durumu -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Stok Durumu</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Gönderen Lokasyondaki Stok:</label>
                                @php
                                    $fromStock = \App\Models\Stock::where('product_id', $stockRequest->product_id)
                                        ->where('location_id', $stockRequest->fromLocation->id)
                                        ->first();
                                @endphp
                                @if($fromStock)
                                    <p>
                                        <span class="badge bg-{{ $fromStock->quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $fromStock->quantity }} {{ $stockRequest->product->unit }}
                                        </span>
                                    </p>
                                @else
                                    <p><span class="badge bg-danger">Stok bulunamadı</span></p>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Alıcı Lokasyondaki Stok:</label>
                                @php
                                    $toStock = \App\Models\Stock::where('product_id', $stockRequest->product_id)
                                        ->where('location_id', $stockRequest->toLocation->id)
                                        ->first();
                                @endphp
                                @if($toStock)
                                    <p>
                                        <span class="badge bg-{{ $toStock->quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $toStock->quantity }} {{ $stockRequest->product->unit }}
                                        </span>
                                    </p>
                                @else
                                    <p><span class="badge bg-danger">Stok bulunamadı</span></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- İşlem Geçmişi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>İşlem Geçmişi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Oluşturulma Tarihi:</label>
                            <p>{{ $stockRequest->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        @if($stockRequest->responded_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Yanıt Tarihi:</label>
                                <p>{{ $stockRequest->responded_at->format('d.m.Y H:i') }}</p>
                            </div>
                        @endif

                        @if($stockRequest->response_notes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Yanıt Notları:</label>
                                <p>{{ $stockRequest->response_notes }}</p>
                            </div>
                        @endif

                        @if($stockRequest->status == 'approved' && $stockRequest->approved_quantity)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Onaylanan Miktar:</label>
                                <p><span class="badge bg-success">{{ $stockRequest->approved_quantity }} {{ $stockRequest->product->unit }}</span></p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hızlı İşlemler -->
                <div class="card">
                    <div class="card-header">
                        <h6>Hızlı İşlemler</h6>
                    </div>
                    <div class="card-body">
                        <!-- Hata Mesajları -->
                        @if(session('error'))
                            <div class="alert alert-danger mb-3">
                                <strong>Hata:</strong> {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success mb-3">
                                <strong>Başarılı:</strong> {{ session('success') }}
                            </div>
                        @endif

                        <!-- Debug Bilgisi -->
                        <div class="alert alert-info mb-3">
                            <strong>Debug:</strong><br>
                            İstek ID: {{ $stockRequest->id }}<br>
                            Durum: {{ $stockRequest->status }}<br>
                            Gönderen: {{ $stockRequest->fromLocation->name ?? 'N/A' }} (ID: {{ $stockRequest->from_location_id }})<br>
                            Alıcı: {{ $stockRequest->toLocation->name ?? 'N/A' }} (ID: {{ $stockRequest->to_location_id }})<br>
                            Bekleyen: {{ $stockRequest->isPending() ? 'Evet' : 'Hayır' }}
                        </div>

                        @if($stockRequest->status == 'pending')
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Onayla</button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reddet</button>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                Bu istek zaten {{ $stockRequest->status == 'approved' ? 'onaylanmış' : 'reddedilmiş' }}.
                            </div>
                        @endif

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('stock-requests.edit', $stockRequest) }}" class="btn btn-outline-warning">Düzenle</a>
                            <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-secondary">Geri Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onay Modal -->
    @if($stockRequest->status == 'pending')
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stok İsteğini Onayla</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('stock-requests.approve', $stockRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="approved_quantity" class="form-label">Onaylanan Miktar</label>
                                <input type="number" name="approved_quantity" id="approved_quantity" class="form-control" min="1" max="{{ $stockRequest->requested_quantity }}" value="{{ $stockRequest->requested_quantity }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="response_notes" class="form-label">Notlar</label>
                                <textarea name="response_notes" id="response_notes" class="form-control" rows="3" placeholder="Onay notları..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-success">Onayla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Red Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stok İsteğini Reddet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('stock-requests.reject', $stockRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="response_notes" class="form-label">Red Nedeni <span class="text-danger">*</span></label>
                                <textarea name="response_notes" id="response_notes" class="form-control" rows="3" placeholder="Red nedenini yazın..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-danger">Reddet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
