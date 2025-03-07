@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Non-Aktifkan Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Anggota')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Non-Aktifkan Anggota">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_anggota" id="id_anggota" value="{{ $data ? old('id_anggota',$data->id_anggota) : old('id_anggota') }}">
    </form>
    @if($data)
    <a href="{{ route('anggota.form',['id'=>$data->id_anggota]) }}" class="btn btn-default btn-flat">Kembali</a>
    @endif
    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Non-Aktifkan" theme="danger" {{-- icon="fas fa-lg fa-save" --}} id="deactiveButton"/>
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
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            return Swal.fire('Peringatan','dalam perbaikan','warning')
            const data = $('#formData').serialize()
            $.post("/", data)
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
                        location.href = "{{ route('anggota.main') }}"
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
