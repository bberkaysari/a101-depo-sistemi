<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Detayı - A101 Stok Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="bi bi-box-seam text-primary"></i> Stok Detayı
                        </h1>
                        <p class="text-muted mb-0">Stok bilgilerini görüntüleyin ve yönetin</p>
                    </div>
                    <div>
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Bilgileri -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle"></i> Stok Bilgileri
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Ürün</h6>
                                <p class="mb-3">
                                    <strong>{{ $stock->product->name }}</strong><br>
                                    <small class="text-muted">{{ $stock->product->sku }}</small>
                                </p>
                                
                                <h6 class="text-muted">Kategori</h6>
                                <p class="mb-3">
                                    <span class="badge bg-secondary">{{ $stock->product->category->name }}</span>
                                </p>
                                
                                <h6 class="text-muted">Birim</h6>
                                <p class="mb-3">{{ $stock->product->unit }}</p>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted">Lokasyon</h6>
                                <p class="mb-3">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <strong>{{ $stock->location->name }}</strong><br>
                                    <small class="text-muted">{{ $stock->location->type }}</small>
                                </p>
                                
                                <h6 class="text-muted">Stok Durumu</h6>
                                <p class="mb-3">
                                    @if($stock->quantity > 0)
                                        <span class="badge bg-success">Stokta</span>
                                    @else
                                        <span class="badge bg-danger">Stok Yok</span>
                                    @endif
                                </p>
                                
                                <h6 class="text-muted">Son Güncelleme</h6>
                                <p class="mb-3">
                                    <small>{{ $stock->updated_at->format('d.m.Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Miktarları -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-123"></i> Stok Miktarları
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary mb-1">{{ $stock->quantity }}</h4>
                                    <small class="text-muted">Mevcut Stok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-warning mb-1">{{ $stock->min_quantity }}</h4>
                                    <small class="text-muted">Minimum Stok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-info mb-1">{{ $stock->max_quantity ?: '∞' }}</h4>
                                    <small class="text-muted">Maksimum Stok</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stok Yüzdesi -->
                        @if($stock->max_quantity && $stock->max_quantity > 0)
                            <div class="mt-3">
                                <label class="form-label">Stok Doluluk Oranı</label>
                                <div class="progress">
                                    @php
                                        $percentage = min(100, ($stock->quantity / $stock->max_quantity) * 100);
                                        $bgClass = $percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <div class="progress-bar {{ $bgClass }}" style="width: {{ $percentage }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notlar -->
                @if($stock->notes)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-chat-text"></i> Notlar
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $stock->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sağ Sidebar -->
            <div class="col-lg-4">
                <!-- Hızlı İşlemler -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning"></i> Hızlı İşlemler
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i> Düzenle
                            </a>
                            
                            @if($stock->quantity > 0)
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#updateQuantityModal">
                                    <i class="bi bi-plus-slash-minus"></i> Miktar Güncelle
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStockModal">
                                <i class="bi bi-trash"></i> Sil
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stok İstatistikleri -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up"></i> İstatistikler
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-muted">Stok Durumu</h6>
                                <p class="mb-0">
                                    @if($stock->isLowStock())
                                        <span class="badge bg-warning">Düşük Stok</span>
                                    @elseif($stock->isOutOfStock())
                                        <span class="badge bg-danger">Stok Yok</span>
                                    @else
                                        <span class="badge bg-success">Normal</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Oluşturulma</h6>
                                <p class="mb-0">
                                    <small>{{ $stock->created_at->format('d.m.Y') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Miktar Güncelleme Modal -->
    <div class="modal fade" id="updateQuantityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stok Miktarını Güncelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('stocks.update-quantity', $stock) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_quantity" class="form-label">Yeni Miktar</label>
                            <input type="number" name="new_quantity" id="new_quantity" class="form-control" 
                                   value="{{ $stock->quantity }}" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="change_reason" class="form-label">Değişim Nedeni</label>
                            <textarea name="change_reason" id="change_reason" class="form-control" rows="2" 
                                      placeholder="Miktar değişiminin nedeni..."></textarea>
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

    <!-- Silme Onay Modal -->
    <div class="modal fade" id="deleteStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stok Kaydını Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bu stok kaydını silmek istediğinizden emin misiniz?</p>
                    <p class="text-danger">
                        <strong>{{ $stock->product->name }}</strong> ürününün 
                        <strong>{{ $stock->location->name }}</strong> lokasyonundaki stok bilgisi kalıcı olarak silinecektir.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Sil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
