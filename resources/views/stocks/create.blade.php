<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Ekle - A101 Stok Yönetim Sistemi</title>
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
                            <i class="bi bi-box-seam text-primary"></i> Stok Ekle
                        </h1>
                        <p class="text-muted mb-0">Yeni stok kaydı oluşturun</p>
                    </div>
                    <div>
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-plus-circle"></i> Stok Bilgileri
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('stocks.store') }}" method="POST">
                            @csrf
                            
                            <!-- Ürün Seçimi -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="product_id" class="form-label">
                                        <i class="bi bi-box"></i> Ürün <span class="text-danger">*</span>
                                    </label>
                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                        <option value="">Ürün seçin...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Lokasyon <span class="text-danger">*</span>
                                    </label>
                                    <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                        <option value="">Lokasyon seçin...</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stok Miktarları -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="quantity" class="form-label">
                                        <i class="bi bi-123"></i> Mevcut Stok <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity', 0) }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="min_quantity" class="form-label">
                                        <i class="bi bi-exclamation-triangle"></i> Minimum Stok
                                    </label>
                                    <input type="number" name="min_quantity" id="min_quantity" class="form-control @error('min_quantity') is-invalid @enderror" 
                                           value="{{ old('min_quantity', 0) }}" min="0">
                                    @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="max_quantity" class="form-label">
                                        <i class="bi bi-arrow-up-circle"></i> Maksimum Stok
                                    </label>
                                    <input type="number" name="max_quantity" id="max_quantity" class="form-control @error('max_quantity') is-invalid @enderror" 
                                           value="{{ old('max_quantity', 0) }}" min="0">
                                    @error('max_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notlar -->
                            <div class="mb-3">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-chat-text"></i> Notlar
                                </label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Stok hakkında ek bilgiler...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> İptal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Stok Ekle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
