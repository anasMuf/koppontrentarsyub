@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Produk')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Produk')

{{-- Content body: main page content --}}

@section('content_body')
<div class="row">
    <form action="" method="post" id="formData">
        @csrf
        <div class="col-md-4">
            <x-adminlte-card title="Form Produk">
                <input type="hidden" name="id_produk" id="id_produk" value="{{ $data ? old('id_produk',$data->id_produk) : old('id_produk') }}">
                <div class="row">
                    <x-adminlte-input name="nama_produk" label="Nama Produk" placeholder="Tulis Nama Produk" id="nama_produk"
                        fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_produk',$data->nama_produk) : old('nama_produk') }}"/>
                </div>
                <div class="row">
                    <x-adminlte-select name="kategori_produk_id" label="Kategori" fgroup-class="col-md-12">
                        <x-adminlte-options :options="$kategori_produk"
                        selected="{{ $data ? old('kategori_produk_id',$data->kategori_produk_id) : old('kategori_produk_id') }}" empty-option=".:: Pilih Kategori ::."/>
                    </x-adminlte-select>
                </div>
                <div class="row">
                    <x-adminlte-textarea name="deskripsi" label="Deskripsi Produk" placeholder="Tulis Deskripsi Produk" id="deskripsi"
                        fgroup-class="col-md-12" disable-feedback enable-old-support error-key>
                        {{ $data ? old('deskripsi',$data->deskripsi) : old('deskripsi') }}
                    </x-adminlte-textarea>
                </div>
            </x-adminlte-card>
        </div>
        <div class="col-md-8">
            <x-adminlte-card title="Other">
                <!-- Toggle for Variants -->
                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants" @checked($data && $data->is_varian)>
                        <label class="custom-control-label" for="has_variants">Produk memiliki varian</label>
                    </div>
                </div>
                <!-- Single Product (No Variants) -->
                <div id="single-product-section" class="mb-3" style="{{ $data && $data->is_varian ? 'display: none;' : '' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="single_stock">Stok</label>
                                <input @disabled($data && $data->is_varian) type="hidden" name="id_produk_varian" value="{{ $data ? old('id_produk_varian',$data->produk_varian[0]->id_produk_varian) : old('id_produk_varian') }}">
                                <input @disabled($data && $data->is_varian) type="number" name="single_stock" value="{{ $data && !$data->is_varian ? old('single_stock',$data->produk_varian[0]->stok_sekarang) : old('single_stock') }}" id="single_stock" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="single_price">Harga Jual</label>
                                <input @disabled($data && $data->is_varian) type="text" name="single_price" value="{{ $data && !$data->is_varian ? old('single_price',number_format($data->produk_varian[0]->harga_jual,0,',','.')) : old('single_price') }}" id="single_price" class="form-control currency-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multiple Variants Section -->
                <div id="variants-section" class="mb-3" style="{{ $data && $data->is_varian ? '' : 'display: none;' }}">
                    <h5>Varian Produk</h5>
                    <div id="variants-container">
                        @if ($data && $data->is_varian)
                            @foreach ($data->produk_varian as $i => $item)
                            <div class="variant-item mb-3 border p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Varian</label>
                                            <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="text" name="variants[{{ $i }}][name]" value="{{ $item->nama_produk_varian }}" class="form-control" placeholder="contoh: Merah, XL, dll">
                                            <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="hidden" name="variants[{{ $i }}][id_produk_varian]" value="{{ $item->id_produk_varian }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Stok</label>
                                            <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="number" name="variants[{{ $i }}][stock]" value="{{ $item->stok_sekarang }}" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Harga Jual</label>
                                            <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="text" name="variants[{{ $i }}][price]" value="{{ number_format($item->harga_jual,0,',','.') }}" class="form-control currency-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-variant" {{ count($data->produk_varian) <= 1 ? 'style=display:none;' : '' }}>Hapus</button>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="variant-item mb-3 border p-3 rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nama Varian</label>
                                        <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="text" name="variants[0][name]" class="form-control" placeholder="contoh: Merah, XL, dll">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Stok</label>
                                        <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="number" name="variants[0][stock]" class="form-control" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Harga Jual</label>
                                        <input {{ $data && $data->is_varian ? '' : 'disabled' }} type="text" name="variants[0][price]" class="form-control currency-input">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-2">
                                <button type="button" class="btn btn-sm btn-danger remove-variant" style="display: none;">Hapus</button>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-success" id="add-variant">Tambah Varian</button>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        @can('produk-view')
        <a href="{{ route('produk.main') }}" class="btn btn-default btn-flat">Kembali</a>
        @endcan
        <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
    </div>
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize currency format for initial inputs
            initCurrencyInput();

            // Toggle between single product and variants
            $('#has_variants').change(function() {
                if($(this).is(':checked')) {
                    $('#single-product-section').hide();
                    $('#single-product-section input').prop('disabled',true);
                    $('#variants-section').show();
                    $('#variants-section input').prop('disabled',false);
                } else {
                    $('#single-product-section').show();
                    $('#single-product-section input').prop('disabled',false);
                    $('#variants-section').hide();
                    $('#variants-section input').prop('disabled',true);
                }
            });

            // Add new variant
            $('#add-variant').click(function() {
                const variantCount = $('.variant-item').length;
                const newVariant = $('.variant-item').first().clone();

                // Update the input names with new index
                newVariant.find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[0\]/g, `[${variantCount}]`);
                        $(this).attr('name', newName);
                        $(this).val(''); // Clear values
                    }
                });

                // Show the remove button for all variants after adding one
                $('.remove-variant').show();
                newVariant.find('.remove-variant').show();

                // Append the new variant
                $('#variants-container').append(newVariant);

                // Initialize currency format for the new variant
                initCurrencyInput();
            });

            // Remove variant (using event delegation)
            $('#variants-container').on('click', '.remove-variant', function() {
                if ($('.variant-item').length > 1) {
                    $(this).closest('.variant-item').remove();

                    // If only one variant remains, hide its remove button
                    if ($('.variant-item').length === 1) {
                        $('.remove-variant').hide();
                    }

                    // Reindex the remaining variants
                    reindexVariants();
                }
            });

            // Function to initialize currency input format
            function initCurrencyInput() {
                $('.currency-input').each(function() {
                    if (!$(this).data('cleave-initialized')) {
                        new Cleave($(this), {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand',
                            numeralDecimalMark: ',',
                            delimiter: '.'
                        });
                        $(this).data('cleave-initialized', true);
                    }
                });
            }

            // Function to reindex variants after removal
            function reindexVariants() {
                $('.variant-item').each(function(index) {
                    $(this).find('input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/variants\[\d+\]/g, `variants[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }
        });
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();

            $('.currency-input').each(function() {
                const numericValue = $(this).val().replace(/\./g, '').replace(/,/g, '.');
                $(this).val(numericValue);
            });

            const data = new FormData(this);

            $.ajax({
                url: "{{ route('produk.store') }}",
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
                            location.href = "{{ route('produk.main') }}"
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
