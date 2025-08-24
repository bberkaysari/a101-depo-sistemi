<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İsteklerim - A101 Stok Sistemi</title>
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
                <h2>İsteklerim</h2>
                <p>Oluşturduğum stok isteklerini görüntüleyin</p>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6>Hızlı Erişim</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stock-requests.create') }}" class="btn btn-success w-100">Yeni İstek</a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stock-requests.index') }}" class="btn btn-info w-100">Tüm İstekler</a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stock-requests.incoming-requests') }}" class="btn btn-warning w-100">Gelen İstekler</a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/stocks" class="btn btn-primary w-100">Stok Durumları</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstek Listesi -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6>İstek Listem</h6>
                    </div>
                    <div class="card-body">
                        @if($stockRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Gönderen</th>
                                            <th>Alıcı</th>
                                            <th>Miktar</th>
                                            <th>Durum</th>
                                            <th>Tarih</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockRequests as $request)
                                            <tr>
                                                <td>
                                                    <strong>{{ $request->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $request->product->sku }}</small>
                                                </td>
                                                <td>
                                                    {{ $request->fromLocation->name }}
                                                </td>
                                                <td>
                                                    {{ $request->toLocation->name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $request->requested_quantity }} {{ $request->product->unit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                        <span class="badge bg-warning">Beklemede</span>
                                                    @elseif($request->status == 'approved')
                                                        <span class="badge bg-success">Onaylandı</span>
                                                    @elseif($request->status == 'rejected')
                                                        <span class="badge bg-danger">Reddedildi</span>
                                                    @elseif($request->status == 'completed')
                                                        <span class="badge bg-primary">Tamamlandı</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $request->created_at->format('d.m.Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('stock-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">Görüntüle</a>
                                                        @if($request->status == 'pending')
                                                            <a href="{{ route('stock-requests.edit', $request) }}" class="btn btn-sm btn-outline-warning">Düzenle</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination yok - tüm istekler tek sayfada gösteriliyor -->
                        @else
                            <div class="text-center py-5">
                                <h4 class="mt-3">Henüz stok isteği oluşturmadınız</h4>
                                <p class="text-muted">İlk stok isteğinizi oluşturmak için aşağıdaki butona tıklayın.</p>
                                <a href="{{ route('stock-requests.create') }}" class="btn btn-primary">İlk İsteğinizi Oluşturun</a>
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
