@extends('layouts.app')

@section('title', 'Stok İstekleri - A101 Stok Sistemi')

@section('content')
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6">
                    <i class="bi bi-arrow-left-right"></i> Stok İstekleri
                </h1>
                <p class="text-muted">Tüm stok isteklerini görüntüleyin ve yönetin</p>
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
                                <a href="{{ route('stock-requests.create') }}" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Yeni İstek
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stock-requests.my-requests') }}" class="btn btn-info w-100">
                                    <i class="bi bi-person"></i> İsteklerim
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('stock-requests.incoming-requests') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-inbox"></i> Gelen İstekler
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/stocks" class="btn btn-primary w-100">
                                    <i class="bi bi-archive"></i> Stok Durumları
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok İstekleri Listesi -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-list"></i> İstek Listesi
                        </h6>
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
                                                    <i class="bi bi-geo-alt text-primary"></i>
                                                    {{ $request->fromLocation->name }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-geo-alt text-success"></i>
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
                                                        <a href="{{ route('stock-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if($request->status == 'pending')
                                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Onay Modal -->
                                            @if($request->status == 'pending')
                                                <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Stok İsteğini Onayla</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('stock-requests.approve', $request) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Ürün: <strong>{{ $request->product->name }}</strong></label>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Gönderen: <strong>{{ $request->fromLocation->name }}</strong></label>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Alıcı: <strong>{{ $request->toLocation->name }}</strong></label>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="approved_quantity" class="form-label">Onaylanan Miktar</label>
                                                                        <input type="number" name="approved_quantity" id="approved_quantity" class="form-control" min="1" max="{{ $request->requested_quantity }}" value="{{ $request->requested_quantity }}" required>
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
                                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Stok İsteğini Reddet</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('stock-requests.reject', $request) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Ürün: <strong>{{ $request->product->name }}</strong></label>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Gönderen: <strong>{{ $request->fromLocation->name }}</strong></label>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Alıcı: <strong>{{ $request->toLocation->name }}</strong></label>
                                                                    </div>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination yok - tüm istekler tek sayfada gösteriliyor -->
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-arrow-left-right display-1 text-muted"></i>
                                <h4 class="mt-3">Henüz stok isteği bulunmuyor</h4>
                                <p class="text-muted">İlk stok isteğini oluşturmak için aşağıdaki butona tıklayın.</p>
                                <a href="{{ route('stock-requests.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> İlk İsteği Oluştur
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@endsection
