@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Kategori Produk')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori Produk')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Kategori Produk">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_kategori_produk" id="id_kategori_produk" value="{{ $data ? old('id_kategori_produk',$data->id_kategori_produk) : old('id_kategori_produk') }}">
        <div class="row">
            <x-adminlte-input name="kategori_produk" label="Kategori Produk" placeholder="Tulis Kategori Produk" id="kategori_produk"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('kategori_produk',$data->kategori_produk) : old('kategori_produk') }}"/>
            <x-adminlte-textarea name="deskripsi" label="Keterangan" placeholder="Tulis Keterangan" id="deskripsi"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key>
                {{ $data ? old('deskripsi',$data->deskripsi) : old('deskripsi') }}
            </x-adminlte-textarea>
        </div>

        @if($data)
        <div class="row">
            <x-adminlte-select name="is_aktif" label="Status Aktif" fgroup-class="col-md-6">
                <x-adminlte-options :options="[
                    [
                        'text' => 'Aktif',
                        'value' => '1',
                    ],
                    [
                        'text' => 'Non-Aktif',
                        'value' => '0',
                    ],
                ]"
                selected="{{ $data ? old('is_aktif',$data->is_aktif) : old('is_aktif') }}" empty-option=".:: Pilih Status Aktif ::."/>
            </x-adminlte-select>
        </div>
        @endif
    </form>
    @can('kategori_produk-view')
    <a href="{{ route('kategori_produk.main') }}" class="btn btn-default btn-flat">Kembali</a>
    @endcan
    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize()
            $.post("{{ route('kategori_produk.store') }}", data)
            .done(function (response) {
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
                        location.href = "{{ route('kategori_produk.main') }}"
                    }, timer);
                }else{
                    let message = response.message;
                        $.each(response.message_validation, function (i,msg) {
                            message += msg[0]+', <br>';
                        })
                    Swal.fire('Peringatan', message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        });
    </script>
@endpush
