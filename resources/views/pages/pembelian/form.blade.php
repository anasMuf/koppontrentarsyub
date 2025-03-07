@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Buat Pembelian')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Buat Pembelian')

{{-- Content body: main page content --}}

@section('content_body')
<div class="row">
    <form action="" method="post" id="formData">
        @csrf
        <div class="col-md-4">
            <x-adminlte-card title="Form Pembelian">
                <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ $data ? old('id_pembelian',$data->id_pembelian) : old('id_pembelian') }}">
                <div class="row">
                    <x-adminlte-input name="no_faktur" label="No Faktur" placeholder="Tulis No Faktur" id="no_faktur"
                        fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('no_faktur',$data->no_faktur) : old('no_faktur') }}"/>
                </div>
                <div class="row">
                    <x-adminlte-select name="supplier_id" label="Supplier" fgroup-class="col-md-12">
                        <x-adminlte-options :options="$supplier"
                        selected="{{ $data ? old('supplier_id',$data->supplier_id) : old('supplier_id') }}" empty-option=".:: Pilih Supplier ::."/>
                    </x-adminlte-select>
                </div>
                <div class="row">
                    <x-adminlte-input type="date" name="tgl_pembelian" label="Tanggal Pembelian" placeholder="Tulis Tanggal Pembelian" id="tgl_pembelian"
                        fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('tgl_pembelian',date('Y-m-d',$data->tgl_pembelian)) : old('tgl_pembelian',date('Y-m-d')) }}"/>
                </div>
            </x-adminlte-card>
        </div>
        <div class="col-md-8">
            <x-adminlte-card title="Detail Pembelian">
                <table class="table table-bordered" id="purchaseItemsTable">
                    <thead>
                        <tr>
                            <th width="30%">Produk</th>
                            <th width="15%">Jumlah</th>
                            <th width="20%">Harga</th>
                            <th width="20%">Subtotal</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows will be added dynamically -->
                        @if ($data)
                        @foreach ($data->pembelian_detail as $i => $item)
                            <tr class="product-row" data-index="{{ $i }}" data-product-id="{{ $item->produk_varian_id }}">
                                <td>
                                    <input type="hidden" name="items[{{ $i }}][id_pembelian_detail]" value="{{ $item->id_pembelian_detail }}">
                                    <input type="hidden" name="items[{{ $i }}][produk_varian_id]" value="{{ $item->produk_varian_id }}">
                                    <div class="product-info">
                                        <strong>{{ $item->produk_varian->nama_produk_varian??$item->produk_varian->produk->nama_produk }}</strong><br>
                                        <small class="text-muted">Code: {{ $item->produk_varian->produk->kode_produk }}</small>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity"
                                        name="items[{{ $i }}][quantity]" min="1" value="{{ $item->qty }}" required>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control cost-price"
                                            name="items[{{ $i }}][cost_price]" min="0" step="100" value="{{ round($item->harga_satuan) }}" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" class="form-control item-total" value="{{ number_format($item->sub_total,0,',','.') }}" readonly>
                                        <input type="hidden" name="items[{{ $i }}][subtotal]" class="item-total-input" value="{{ round($item->sub_total) }}">
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <button type="button" class="btn btn-success btn-sm" id="btnAddItem" data-toggle="modal" data-target="#modalCariProduk">
                                    <i class="fa fa-plus"></i> Add Item
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="table-responsive">
                            <table class="table">
                                <tr class="font-weight-bold">
                                    <th>Total:</th>
                                    <td class="text-right">
                                        <span id="total">{{ ($data) ? number_format($data->total_pembelian,0,',','.') : 0 }}</span>
                                        <input type="hidden" name="total" id="total_input" value="{{ ($data) ? $data->total_pembelian : 0 }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($data)
                <div class="row">
                    <x-adminlte-select name="status_pembelian" label="Status Pembelian" fgroup-class="col-md-12">
                        <x-adminlte-options :options="$status_pembelian"
                        selected="{{ old('status_pembelian',$data->status_pembelian) }}" empty-option=".:: Pilih Status Pembelian ::."/>
                    </x-adminlte-select>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end" style="gap: 10px">
                        @can('pembelian-view')
                        <a href="{{ route('pembelian.main') }}" class="btn btn-default btn-flat">Kembali</a>
                        @endcan
                        <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
                        @if ($data)
                        <a href="{{ route('pembelian.print', ['id'=>$data->id_pembelian]) }}" class="btn btn-warning btn-flat">Print</a>
                        @endif
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </form>
</div>

{{-- Modal cari produk --}}
<x-adminlte-modal id="modalCariProduk" title="Cari Produk" size="lg"
     v-centered static-backdrop scrollable>
    <div class="row">
        <x-adminlte-input name="cariProduk" placeholder="Cari" id="cariProduk" autofocus
        fgroup-class="col-md-12">
            <x-slot name="appendSlot">
                <x-adminlte-button theme="primary" icon="fas fa-search" id="btnCariProduk"/>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div id="searchResults" class="mt-3">
        <div class="table-responsive">
            <table class="table table-bordered" id="productsTable">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Kategori</th>
                        <th>Nama Produk</th>
                        <th>Varian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Search results will be displayed here -->
                </tbody>
            </table>
        </div>
        <div id="noResults" class="alert alert-info d-none">
            Tidak ada produk yang cocok, silahkan tambahkan data produk <a href="{{ route('produk.form') }}">di sini</a>
        </div>
    </div>
    <x-slot name="footerSlot">
        <x-adminlte-button theme="default" label="Tutup" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        $(document).ready(function() {
            var currentRowIndex = {{ ($data) ? count($data->pembelian_detail) : 0 }};
            attachRowHandlers();
            calculateItemTotal(currentRowIndex - 1);
            calculateTotals();

            // Add Item Button Click
            $('#btnAddItem').click(function() {
                showProductSearchModal();
            });

            // Show Product Search Modal
            function showProductSearchModal() {
                $('#cariProduk').val('');
                $('#productsTable tbody').empty();
                $('#noResults').addClass('d-none');
                $('#productSearchModal').modal('show');
            }

            // Search Products
            $('#btnCariProduk').click(function() {
                searchProducts();
            });

            // Search when pressing Enter
            $('#cariProduk').keypress(function(e) {
                if (e.which == 13) {
                    searchProducts();
                    return false;
                }
            });

            // Search Products Function
            function searchProducts() {
                var searchTerm = $('#cariProduk').val();

                // Clear previous results
                $('#productsTable tbody').empty();
                $('#noResults').addClass('d-none');

                if (searchTerm.trim() === '') {
                    $('#noResults').removeClass('d-none').text('Tuliskan pencarian');
                    return;
                }

                // Show loading indicator
                $('#noResults').removeClass('d-none').text('Mencari...');

                // AJAX request to search products
                $.ajax({
                    url: "{{ route('produk.search') }}",
                    type: "GET",
                    data: { term: searchTerm },
                    dataType: "json",
                    success: function(response) {
                        $('#noResults').addClass('d-none');

                        if (response.data.length === 0) {
                            $('#noResults').removeClass('d-none').html(`Tidak ada produk yang cocok, silahkan tambahkan data produk <a href="${"{{ route('produk.form') }}"}">di sini</a>`);
                            return;
                        }

                        $('#productsTable tbody').empty()
                        // Populate results table
                        $.each(response.data, function(index, produk) {
                            if (produk.is_varian) {
                                // Produk dengan varian
                                let totalVarian = produk.produk_varian.length;

                                $.each(produk.produk_varian, function(i, varian) {
                                    let row = '<tr>';

                                    // Hanya tampilkan kolom dengan rowspan pada baris pertama varian
                                    if (i === 0) {
                                        row += `<td rowspan="${totalVarian}">${produk.kode_produk || ''}</td>`;
                                        row += `<td rowspan="${totalVarian}">${produk.kategori_produk ? produk.kategori_produk.kategori_produk : 'N/A'}</td>`;
                                        row += `<td rowspan="${totalVarian}">${produk.nama_produk}</td>`;
                                    }

                                    row += `<td>${varian.nama_produk_varian || '-'}</td>`;
                                    row += `<td>
                                            <button type="button" class="btn btn-sm btn-primary selectProduct"
                                                    data-id="${varian.id_produk_varian}"
                                                    data-code="${produk.kode_produk || ''}"
                                                    data-name="${varian.nama_produk_varian || produk.nama_produk}">
                                                Select
                                            </button>
                                        </td>`;
                                    row += '</tr>';

                                    $('#productsTable tbody').append(row);
                                });
                            } else {
                                // Produk tanpa varian (mengambil varian pertama sebagai data utama)
                                if (produk.produk_varian && produk.produk_varian.length > 0) {
                                    let varian = produk.produk_varian[0];
                                    let row = `
                                        <tr>
                                            <td>${produk.kode_produk || ''}</td>
                                            <td>${produk.kategori_produk ? produk.kategori_produk.kategori_produk : 'N/A'}</td>
                                            <td>${produk.nama_produk}</td>
                                            <td>-</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary selectProduct"
                                                        data-id="${varian.id_produk_varian}"
                                                        data-code="${produk.kode_produk || ''}"
                                                        data-name="${produk.nama_produk}">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                    $('#productsTable tbody').append(row);
                                }
                            }
                        });

                        // Attach click handlers to select buttons
                        attachSelectHandlers();
                    },
                    error: function(xhr) {
                        $('#noResults').removeClass('d-none').text('Terjadi Kesalahan. Refresh halaman atau ulangi pencarian.');
                        console.error('Terjadi Kesalahan:', xhr);
                    }
                });
            }

            // Attach handlers to select product buttons
            function attachSelectHandlers() {
                $('.selectProduct').click(function() {
                    var productId = $(this).data('id');
                    var productCode = $(this).data('code');
                    var productName = $(this).data('name');

                    addProductToTable(productId, productCode, productName);
                    $('#productSearchModal').modal('hide');
                });
            }

            // Add Product to Table
            function addProductToTable(productId, productCode, productName) {
                var isDuplicate = false;

                // Cari di semua baris tabel apakah product ID sudah ada
                $('.product-row').each(function() {
                    var existingProductId = $(this).data('product-id');
                    if (existingProductId == productId) {
                        isDuplicate = true;
                        return false; // Hentikan loop each
                    }
                });

                // Jika duplikat, tampilkan pesan error dan hentikan proses
                if (isDuplicate) {
                    Swal.fire('Error', 'Produk ini sudah ditambahkan ke dalam daftar pembelian.', 'error');
                    return;
                }

                var newRow = `
                    <tr class="product-row" data-index="${currentRowIndex}" data-product-id="${productId}">
                        <td>
                            <input type="hidden" name="items[${currentRowIndex}][produk_varian_id]" value="${productId}">
                            <div class="product-info">
                                <strong>${productName}</strong><br>
                                <small class="text-muted">Code: ${productCode}</small>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity"
                                name="items[${currentRowIndex}][quantity]" min="1" value="1" required>
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control cost-price"
                                    name="items[${currentRowIndex}][cost_price]" min="0" step="100" value="0" required>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control item-total" value="0" readonly>
                                <input type="hidden" name="items[${currentRowIndex}][subtotal]" class="item-total-input" value="0">
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#purchaseItemsTable tbody').append(newRow);

                // Increment row index for next item
                currentRowIndex++;

                // Attach event handlers to new row
                attachRowHandlers();

                // Calculate initial totals
                calculateItemTotal(currentRowIndex - 1);
                calculateTotals();
            }

            // Attach handlers to row elements
            function attachRowHandlers() {
                // Quantity change
                $('.quantity').off('change').on('change', function() {
                    var row = $(this).closest('tr');
                    var index = row.data('index');
                    calculateItemTotal(index);
                    calculateTotals();
                });

                // Cost price change
                $('.cost-price').off('change keyup').on('change keyup', function() {
                    var row = $(this).closest('tr');
                    var index = row.data('index');
                    calculateItemTotal(index);
                    calculateTotals();
                });

                // Remove item button
                $('.remove-item').off('click').on('click', function() {
                    $(this).closest('tr').remove();
                    calculateTotals();
                });
            }

            // Calculate item total
            function calculateItemTotal(index) {
                var row = $('tr[data-index="' + index + '"]');
                var quantity = parseFloat(row.find('.quantity').val()) || 0;
                var costPrice = parseFloat(row.find('.cost-price').val()) || 0;
                var total = quantity * costPrice;

                row.find('.item-total').val(formatRupiah(total));
                row.find('.item-total-input').val(total);
            }

            // Calculate overall totals
            function calculateTotals() {
                var subtotal = 0;

                $('.item-total-input').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                var total = subtotal;

                $('#total').text(formatRupiah(total));
                $('#total_input').val(total);
            }
        });
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();

            if ($('#purchaseItemsTable tbody tr').length === 0) {
                Swal.fire('Peringatan','Please add at least one product to the purchase','warning');
                return false;
            }

            const data = new FormData(this);

            $.ajax({
                url: "{{ route('pembelian.store') }}",
                type: "post",
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success){
                        let timer = 1500
                        Swal.fire({
                            title: "Sukses",
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: timer
                        });
                        setTimeout(() => {
                            location.href = "{{ route('pembelian.main') }}"

                            // location.href = "/pembelian/form?id="+
                        }, timer);
                    }else{
                        let message = response.message;
                            $.each(response.message_validation, function (i,msg) {
                                message += msg[0]+', <br>';
                            })
                        Swal.fire('Peringatan', message, 'warning')
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Terjadi Kesalahan', error, 'error')
                }
            });
        });
    </script>
@endpush
