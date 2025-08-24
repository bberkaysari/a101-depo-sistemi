@extends('layouts.app')

@section('title', 'Stok İsteği Oluştur - A101 Stok Sistemi')

@section('content')
        <div class="row mb-4">
            <div class="col-12">
                <h2>Stok İsteği Oluştur</h2>
                <p>Diğer lokasyonlardan stok isteği oluşturun</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6>Yeni Stok İsteği</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($errors) && !$errors->isEmpty())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('stock-requests.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="product_id" class="form-label">Ürün <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="">Ürün Seçin</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="requested_quantity" class="form-label">İstenen Miktar <span class="text-danger">*</span></label>
                                    <input type="number" name="requested_quantity" id="requested_quantity" class="form-control" min="1" value="{{ old('requested_quantity') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_location_id" class="form-label">Gönderen Lokasyon <span class="text-danger">*</span></label>
                                    <select name="from_location_id" id="from_location_id" class="form-select" required>
                                        <option value="">Lokasyon Seçin</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('from_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Stok bulunan lokasyon</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="to_location_display" class="form-label">Alıcı Lokasyon</label>
                                    <input type="text" id="to_location_display" class="form-control" value="{{ Auth::user()->location->name }}" readonly>
                                    <small class="text-muted">Stok isteği otomatik olarak kendi lokasyonunuza gönderilecek</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="request_notes" class="form-label">İstek Notları</label>
                                <textarea name="request_notes" id="request_notes" class="form-control" rows="3" placeholder="İstek hakkında detayları yazın...">{{ old('request_notes') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary">← Geri Dön</a>
                                <button type="submit" class="btn btn-primary">✔ İsteği Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stok Durumu Bilgisi -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle"></i> Stok Durumu Bilgisi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-lightbulb"></i> Nasıl Çalışır?</h6>
                            <ol class="mb-0">
                                <li>Gönderen lokasyonda yeterli stok bulunmalıdır</li>
                                <li>İstek gönderildikten sonra gönderen lokasyon onayı bekler</li>
                                <li>Onaylandıktan sonra stok transferi gerçekleşir</li>
                                <li>Transfer tamamlandığında stok miktarları otomatik güncellenir</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
