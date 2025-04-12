@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Poin of Sell')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Poin of Sell')

@section('classes_body')
{{ 'sidebar-mini sidebar-collapse' }}
@endsection

{{-- Content body: main page content --}}

@section('content_body')
<form id="formData">
    @csrf
    <!-- Informasi Transaksi -->
    <x-adminlte-card title="Informasi Transaksi" theme="">
        <div class="row">
            <x-adminlte-input name="no_struk" label="No Struk" placeholder="Tulis No Struk" id="no_struk"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('no_struk',$data->no_struk) : old('no_struk') }}"/>
            <x-adminlte-input type="date" name="tgl_penjualan" label="Tanggal Penjualan" placeholder="Tulis Tanggal Penjualan" id="tgl_pembelian"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('tgl_pembelian',date('Y-m-d',$data->tgl_pembelian)) : old('tgl_pembelian',date('Y-m-d')) }}"/>
            <x-adminlte-select name="anggota_id" label="Anggota"
                fgroup-class="col-md-4">
                <x-adminlte-options :options="$anggota" required
                selected="{{ $data ? old('anggota_id',$data->anggota_id) : old('anggota_id') }}" empty-option=".:: Pilih Anggota ::."/>
            </x-adminlte-select>
        </div>
    </x-adminlte-card>

    <!-- Panel Produk -->
    <div class="row mt-3">
        <div class="col-md-7">
            <x-adminlte-card title="Daftar Produk" theme="">
                <div class="row">
                    <x-adminlte-input name="cariProduk" placeholder="Cari nama produk" id="cariProduk" autofocus
                    fgroup-class="col-md-12">
                        <x-slot name="appendSlot">
                            <x-adminlte-button theme="primary" icon="fas fa-search" id="btnCariProduk"/>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="row" id="product_container">
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
                            <div class="card product-card" data-id="{{ $item->produk_varian[0]->id_produk_varian }}" data-name="{{ $item->nama_produk }}" data-price="{{ $item->produk_varian[0]->harga_jual }}" data-stock="{{ $item->produk_varian[0]->stok_sekarang }}">
                                <div class="card-body text-center p-2">
                                    <h6 class="card-title mb-1">{{ $item->nama_produk }}</h6>
                                    <p class="card-text text-primary mb-1">Rp {{ number_format($item->produk_varian[0]->harga_jual, 0, ',', '.') }}</p>
                                    <small class="text-muted">Stok: {{ $item->produk_varian[0]->stok_sekarang }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- <div class="row mt-2">
                    <div class="col-12">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-center">
                                {{ $produk->links() }}
                            </ul>
                        </nav>
                    </div>
                </div> --}}
            </x-adminlte-card>
        </div>

        <div class="col-md-5">
            <x-adminlte-card title="Keranjang Belanja" theme="">
                <div class="table-responsive">
                    <table class="table table-bordered" id="cart_table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th width="100">Harga</th>
                                <th width="80">Qty</th>
                                <th width="120">Subtotal</th>
                                <th width="30"></th>
                            </tr>
                        </thead>
                        <tbody id="cart_items">
                            <!-- Item keranjang akan ditambahkan di sini melalui JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div id="empty_cart_message" class="text-center py-3">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p>Keranjang belanja kosong</p>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    <!-- Bagian perhitungan jumlah pembayaran -->
    <div class="card mt-3">
        <x-adminlte-card title="Detail Pembayaran" theme="">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group row mb-3">
                        <label class="col-sm-4 col-form-label font-weight-bold">Total</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control" id="grand_total" name="grand_total" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label class="col-sm-4 col-form-label font-weight-bold">Catatan Transaksi</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="transaction_notes" name="transaction_notes" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <x-adminlte-select name="metode_pembayaran" label="Metode Pembayaran"
                        fgroup-class="col-md-12">
                        <x-adminlte-options :options="$metode_pembayaran"
                        selected="{{ $data ? old('metode_pembayaran',$data->metode_pembayaran) : old('metode_pembayaran') }}" empty-option=".:: Pilih Metode Pembayaran ::."/>
                    </x-adminlte-select>
                    <div class="form-group mb-3">
                        <label>Jumlah Bayar</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" id="amount_paid" name="amount_paid">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Kembalian</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" id="change_amount" name="change_amount" readonly>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="card_details" style="display:none;">
                        <label>Nomor Kartu (4 digit terakhir)</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" maxlength="4" pattern="\d{4}">
                    </div>
                    <div class="form-group mb-3" id="transfer_details" style="display:none;">
                        <label>Referensi Transfer</label>
                        <input type="text" class="form-control" id="transfer_ref" name="transfer_ref">
                    </div>
                </div>
            </div>
            <x-slot name="footerSlot">
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg btn-block" id="complete_sale">
                            <i class="fas fa-check-circle"></i> Selesaikan Transaksi
                        </button>
                        <button type="button" class="btn btn-danger btn-block" id="cancel_sale">
                            <i class="fas fa-times-circle"></i> Batalkan
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-adminlte-card>
    </div>
</form>

<x-adminlte-modal id="variantModal" title="Varian Produk" size="md"
     v-centered static-backdrop scrollable>
     <h6 class="product-name-modal mb-3"></h6>
     <div class="table-responsive">
         <table class="table table-bordered table-hover" id="variants_table">
             <thead>
                 <tr>
                     <th>Varian</th>
                     <th width="120">Harga</th>
                     <th width="80">Stok</th>
                     <th width="80">Aksi</th>
                 </tr>
             </thead>
             <tbody id="variants_container">
                 <!-- Daftar varian akan ditampilkan di sini -->
             </tbody>
         </table>
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
            let currentIndexRow = 0;
            // Memeriksa apakah keranjang kosong dan menampilkan pesan jika iya
            function checkEmptyCart() {
                if ($('#cart_items tr').length === 0) {
                    $('#empty_cart_message').show();
                    $('#cart_table').hide();
                } else {
                    $('#empty_cart_message').hide();
                    $('#cart_table').show();
                }
            }

            // Memperbarui nomor baris pada tabel keranjang
            function updateRowNumbers() {
                $('#cart_items tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Menambahkan produk ke keranjang
            $(document).on('click', '.product-card', function() {
                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let productPrice = $(this).data('price');
                let productStock = $(this).data('stock');

                // Periksa apakah produk sudah ada di keranjang
                let existingItem = $('#cart_items tr[data-id="' + productId + '"]');

                if (existingItem.length > 0) {
                    // Produk sudah ada, tambahkan kuantitas
                    let qtyInput = existingItem.find('.item-quantity');
                    let currentQty = parseInt(qtyInput.val());

                    if (currentQty < productStock) {
                        qtyInput.val(currentQty + 1);
                        qtyInput.trigger('change'); // Memicu perhitungan ulang
                    } else {
                        Swal.fire('Peringatan','Stok produk tidak mencukupi!','warning');
                    }
                } else {
                    // Produk belum ada, tambahkan baris baru
                    if (productStock > 0) {
                        let newRow = `
                            <tr data-id="${productId}">
                                <td>${productName}</td>
                                <td>
                                    <input type="hidden" name="items[${currentIndexRow}][produk_varian_id]" value="${productId}">
                                    <input type="hidden" name="items[${currentIndexRow}][harga_satuan]" class="item-price" value="${round(productPrice)}">
                                    Rp ${formatRupiah(productPrice)}
                                </td>
                                <td>
                                    <input type="number" name="items[${currentIndexRow}][qty]" class="form-control item-quantity" value="1" min="1" max="${productStock}">
                                </td>
                                <td>
                                    <input type="hidden" name="items[${currentIndexRow}][subtotal]" class="item-total" value="${round(productPrice)}">
                                    Rp <span class="item-total-display">${formatRupiah(productPrice)}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#cart_items').append(newRow);
                        checkEmptyCart();
                        calculatePayment();
                        currentIndexRow += 1;
                    } else {
                        Swal.fire('Peringatan','Produk sedang tidak tersedia (stok: 0)','warning');
                    }
                }
            });

            // Tampilkan modal untuk produk dengan varian
            $(document).on('click', '.select-variant', function() {
                let productId = $(this).closest('.product-card-variant').data('id');
                let productName = $(this).closest('.product-card-variant').data('name');

                // Set judul modal
                $('.product-name-modal').text(productName);

                // Ambil data varian dari server
                $.ajax({
                    url: "{{ route('produk.search') }}",
                    type: 'GET',
                    data: { id: productId },
                    success: function(response) {
                        // Isi tabel varian
                        $('#variants_container').html('');

                        response.variants.forEach(function(variant) {
                            let row = `
                                <tr>
                                    <td>${variant.nama_produk_varian}</td>
                                    <td>Rp ${formatRupiah(variant.harga_jual)}</td>
                                    <td>${variant.stok_sekarang}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm add-variant"
                                            data-product-id="${productId}"
                                            data-product-name="${productName}"
                                            data-variant-id="${variant.id_produk_varian}"
                                            data-variant-name="${variant.nama_produk_varian}"
                                            data-price="${variant.harga_jual}"
                                            data-stock="${variant.stok_sekarang}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#variants_container').append(row);
                        });

                        // Tampilkan modal
                        $('#variantModal').modal('show');
                    }
                });
            });

            // Menambahkan varian ke keranjang
            $(document).on('click', '.add-variant', function() {
                let productId = $(this).data('product-id');
                let productName = $(this).data('product-name');
                let variantId = $(this).data('variant-id');
                let variantName = $(this).data('variant-name');
                let productPrice = $(this).data('price');
                let productStock = $(this).data('stock');
                let fullProductName = productName + ' - ' + variantName;

                // Periksa apakah varian sudah ada di keranjang
                let existingItem = $('#cart_items tr[data-variant-id="' + variantId + '"]');

                if (existingItem.length > 0) {
                    // Varian sudah ada, tambahkan kuantitas
                    let qtyInput = existingItem.find('.item-quantity');
                    let currentQty = parseInt(qtyInput.val());

                    if (currentQty < productStock) {
                        qtyInput.val(currentQty + 1);
                        qtyInput.trigger('change'); // Memicu perhitungan ulang
                    } else {
                        Swal.fire('Peringatan','Stok produk tidak mencukupi!','warning');
                    }
                } else {
                    // Varian belum ada, tambahkan baris baru
                    if (productStock > 0) {
                        let newRow = `
                            <tr data-id="${productId}" data-variant-id="${variantId}">
                                <td>
                                    ${productName}
                                    <small class="d-block text-muted">${variantName}</small>
                                </td>
                                <td>
                                    <input type="hidden" name="items[${currentIndexRow}][produk_varian_id]" value="${variantId}">
                                    <input type="hidden" name="items[${currentIndexRow}][harga_satuan]" class="item-price" value="${round(productPrice)}">
                                    Rp ${formatRupiah(productPrice)}
                                </td>
                                <td>
                                    <input type="number" name="items[${currentIndexRow}][qty]" class="form-control item-quantity" value="1" min="1" max="${productStock}">
                                </td>
                                <td>
                                    <input type="hidden" name="items[${currentIndexRow}][subtotal]" class="item-total" value="${round(productPrice)}">
                                    Rp <span class="item-total-display">${formatRupiah(productPrice)}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#cart_items').append(newRow);
                        checkEmptyCart();
                        calculatePayment();
                        currentIndexRow += 1
                    } else {
                        Swal.fire('Peringatan','Varian sedang tidak tersedia (stok: 0)','warning');
                    }
                }

                // Sembunyikan modal
                $('#variantModal').modal('hide');
            });

            // Menghapus item dari keranjang
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                checkEmptyCart();
                calculatePayment();
            });

            // Menghitung subtotal saat kuantitas berubah
            $(document).on('change', '.item-quantity', function() {
                let row = $(this).closest('tr');
                let price = parseFloat(row.find('.item-price').val());
                let qty = parseInt($(this).val());
                let subtotal = price * qty;

                row.find('.item-total').val(subtotal);
                row.find('.item-total-display').text(formatRupiah(subtotal));

                calculatePayment();
            });

            // Pencarian produk
            $('#btnCariProduk').click(function() {
                let keyword = $('#cariProduk').val();

                $.ajax({
                    url: "{{ route('produk.search') }}",
                    type: 'GET',
                    data: { term: keyword, from: 'pos' },
                    success: function(response) {
                        $('#product_container').html(response.content);
                    }
                });
            });

            // Pencarian produk dengan enter
            $('#cariProduk').keypress(function(e) {
                if (e.which === 13) {
                    $('#btnCariProduk').click();
                    e.preventDefault();
                }
            });

            // Fungsi untuk menghitung total pembayaran
            function calculatePayment() {
                let total = 0;

                // Menghitung total dari semua item
                $('.item-total').each(function() {
                    total += unformatCurrency($(this).val());
                });

                // Menghitung total (grand total)
                $('#grand_total').val(formatRupiah(total));

                // Menghitung kembalian
                let amountPaid = unformatCurrency($('#amount_paid').val()) || 0;
                let changeAmount = amountPaid - total;
                $('#change_amount').val(formatRupiah(changeAmount > 0 ? changeAmount : 0));

                // Memeriksa apakah pembayaran cukup
                // if (amountPaid >= total) {
                //     $('#complete_sale').prop('disabled', false);
                // } else {
                //     $('#complete_sale').prop('disabled', true);
                // }
            }

            // Memicu perhitungan saat ada perubahan pada input
            $(document).on('input', '.item-quantity, .item-price, #discount_percentage, #discount_amount, #tax_percentage, #amount_paid', function() {
                calculatePayment();
            });

            // Memperbarui detail form berdasarkan metode pembayaran
            $('#metode_pembayaran').change(function() {
                let method = $(this).val();

                // Sembunyikan semua form detail pembayaran
                $('#card_details, #transfer_details').hide();

                // Tampilkan form yang sesuai dengan metode pembayaran
                if (method === 'card') {
                    $('#card_details').show();
                } else if (method === 'transfer') {
                    $('#transfer_details').show();
                }

                // Untuk pembayaran tunai dan QRIS, fokus ke input jumlah bayar
                if (method === 'cash' || method === 'qris') {
                    $('#amount_paid').focus();
                }
            });

            // Menangani klik tombol selesaikan transaksi
            $('#complete_sale').click(function() {
                // Validasi input
                let isValid = true;
                let method = $('#metode_pembayaran').val();

                if (!method) {
                    alert('Silakan pilih metode pembayaran');
                    isValid = false;
                } else if (method === 'card' && !$('#card_number').val()) {
                    alert('Masukkan 4 digit terakhir kartu');
                    isValid = false;
                } else if (method === 'transfer' && !$('#transfer_ref').val()) {
                    alert('Masukkan referensi transfer');
                    isValid = false;
                }

                if (isValid) {
                    // Kirim form untuk pemrosesan
                    $('#formData').submit();
                }
            });

            // Menangani klik tombol batalkan
            $('#cancel_sale').click(function() {
                Swal.fire({
                    title: "Batal Transaksi",
                    text: "Apakah Anda yakin ingin membatalkan transaksi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batal!',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: "#d33",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('penjualan.pos') }}"; // Kembali ke halaman POS
                    }
                });
            });

            // Inisialisasi
            checkEmptyCart();
            calculatePayment();
        });
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();

            const data = new FormData(this);

            $.ajax({
                url: "{{ route('penjualan.store') }}",
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
                            location.href = "{{ route('penjualan.pos') }}"

                            // location.href = "/penjualan/form?id="+
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
