@foreach($produk as $item)
@if ($item->is_varian)
<div class="col-md-4 mb-3">
    <div class="card product-card-variant" data-id="{{ $item->id_produk }}" data-name="{{ $item->nama_produk }}">
        <div class="card-body text-center p-2">
            <h6 class="card-title mb-1">{{ $item->nama_produk }}</h6>
            <p class="card-text text-primary mb-1">
                <span class="badge bg-info">{{ count($item->produk_varian) }} varian</span>
            </p>
            <button type="button" class="btn btn-sm btn-outline-primary select-variant">Pilih</button>
        </div>
    </div>
</div>
@else
<div class="col-md-4 mb-3">
    <div class="card product-card" data-id="{{ $item->id_produk }}" data-name="{{ $item->nama_produk }}" data-price="{{ $item->produk_varian[0]->harga_jual }}" data-stock="{{ $item->produk_varian[0]->stok_sekarang }}">
        <div class="card-body text-center p-2">
            <h6 class="card-title mb-1">{{ $item->nama_produk }}</h6>
            <p class="card-text text-primary mb-1">Rp {{ number_format($item->produk_varian[0]->harga_jual, 0, ',', '.') }}</p>
            <small class="text-muted">Stok: {{ $item->produk_varian[0]->stok_sekarang }}</small>
        </div>
    </div>
</div>
@endif
@endforeach
