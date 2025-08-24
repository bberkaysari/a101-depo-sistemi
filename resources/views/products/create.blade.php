<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Ekle - A101 Stok Yönetim Sistemi</title>
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
                            <i class="bi bi-box text-primary"></i> Ürün Ekle
                        </h1>
                        <p class="text-muted mb-0">Yeni ürün kaydı oluşturun</p>
                    </div>
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
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
                            <i class="bi bi-plus-circle"></i> Ürün Bilgileri
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf
                            
                            <!-- Ürün Adı ve SKU -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-tag"></i> Ürün Adı <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control @if(isset($errors) && $errors->has('name')) is-invalid @endif" 
                                           value="{{ old('name') }}" placeholder="Ürün adını girin..." required>
                                    @if(isset($errors) && $errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="sku" class="form-label">
                                        <i class="bi bi-upc"></i> SKU <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="sku" id="sku" class="form-control @if(isset($errors) && $errors->has('sku')) is-invalid @endif" 
                                           value="{{ old('sku') }}" placeholder="SKU kodunu girin..." required>
                                    @if(isset($errors) && $errors->has('sku'))
                                        <div class="invalid-feedback">{{ $errors->first('sku') }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Kategori ve Birim -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">
                                        <i class="bi bi-folder"></i> Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select name="category_id" id="category_id" class="form-select @if(isset($errors) && $errors->has('category_id')) is-invalid @endif" required>
                                        <option value="">Kategori seçin...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($errors) && $errors->has('category_id'))
                                        <div class="invalid-feedback">{{ $errors->first('category_id') }}</div>
                                    @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="unit" class="form-label">
                                        <i class="bi bi-rulers"></i> Birim <span class="text-danger">*</span>
                                    </label>
                                    <select name="unit" id="unit" class="form-select @if(isset($errors) && $errors->has('unit')) is-invalid @endif" required>
                                        <option value="">Birim seçin...</option>
                                        <option value="Adet" {{ old('unit') == 'Adet' ? 'selected' : '' }}>Adet</option>
                                        <option value="Kg" {{ old('unit') == 'Kg' ? 'selected' : '' }}>Kg</option>
                                        <option value="Litre" {{ old('unit') == 'Litre' ? 'selected' : '' }}>Litre</option>
                                        <option value="Metre" {{ old('unit') == 'Metre' ? 'selected' : '' }}>Metre</option>
                                        <option value="Paket" {{ old('unit') == 'Paket' ? 'selected' : '' }}>Paket</option>
                                    </select>
                                    @if(isset($errors) && $errors->has('unit'))
                                        <div class="invalid-feedback">{{ $errors->first('unit') }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Fiyat ve Stok -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">
                                        <i class="bi bi-currency-dollar"></i> Fiyat (₺)
                                    </label>
                                    <input type="number" name="price" id="price" class="form-control @if(isset($errors) && $errors->has('price')) is-invalid @endif" 
                                           value="{{ old('price', 0) }}" min="0" step="0.01" placeholder="0.00">
                                    @if(isset($errors) && $errors->has('price'))
                                        <div class="invalid-feedback">{{ $errors->first('price') }}</div>
                                    @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label">
                                        <i class="bi bi-toggle-on"></i> Durum
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                               value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Açıklama -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi bi-chat-text"></i> Açıklama
                                </label>
                                <textarea name="description" id="description" rows="3" class="form-control @if(isset($errors) && $errors->has('description')) is-invalid @endif" 
                                          placeholder="Ürün hakkında detaylı bilgi...">{{ old('description') }}</textarea>
                                @if(isset($errors) && $errors->has('description'))
                                    <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                                @endif
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> İptal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Ürün Ekle
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
