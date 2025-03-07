@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Supplier')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Supplier')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Supplier">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_supplier" id="id_supplier" value="{{ $data ? old('id_supplier',$data->id_supplier) : old('id_supplier') }}">
        <div class="row">
            <x-adminlte-input name="nama_supplier" label="Nama Supplier" placeholder="Tulis Nama Supplier" id="nama_supplier"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_supplier',$data->nama_supplier) : old('nama_supplier') }}"/>
            <x-adminlte-textarea name="alamat" label="Alamat" placeholder="Tulis Alamat" id="alamat"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key>
                {{ $data ? old('alamat',$data->alamat) : old('alamat') }}
            </x-adminlte-textarea>
        </div>
        <div class="row">
            <x-adminlte-input name="no_telepon" label="No. Telepon" placeholder="Tulis No. Telepon" id="no_telepon" maxLength="15"
            fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('no_telepon',$data->no_telepon) : old('no_telepon') }}"/>
            <x-adminlte-input name="email" label="Email" placeholder="Tulis Email" id="email"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('email',$data->email) : old('email') }}"/>
            <x-adminlte-input name="npwp" label="NPWP" placeholder="Tulis NPWP" id="npwp" maxLength="20"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('npwp',$data->npwp) : old('npwp') }}"/>
        </div>
    </form>
    @can('supplier-view')
    <a href="{{ route('supplier.main') }}" class="btn btn-default btn-flat">Kembali</a>
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
            $.post("{{ route('supplier.store') }}", data)
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
                        location.href = "{{ route('supplier.main') }}"
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
