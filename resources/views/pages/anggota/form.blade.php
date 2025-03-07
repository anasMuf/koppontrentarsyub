@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Anggota')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Anggota">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_anggota" id="id_anggota" value="{{ $data ? old('id_anggota',$data->id_anggota) : old('id_anggota') }}">
        <div class="row">
            <x-adminlte-input name="no_anggota" label="No Anggota" placeholder="Tulis No Anggota" id="no_anggota" readonly
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('no_anggota',$data->no_anggota) : old('no_anggota',$no_anggota) }}"/>
            <x-adminlte-input type="date" name="tgl_bergabung" label="Tanggal Bergabung"  placeholder="Tulis Tanggal Bergabung"  id="tgl_bergabung" readonly
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tgl_bergabung',$data->tgl_bergabung) : old('tgl_bergabung',date('Y-m-d')) }}"/>
        </div>
        <div class="row">
            <x-adminlte-input name="nama_lengkap" label="Nama Lengkap" placeholder="Tulis Nama Lengkap" id="nama_lengkap"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_lengkap',$data->nama_lengkap) : old('nama_lengkap') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input name="no_ktp" label="No. KTP" placeholder="Tulis No. KTP" id="no_ktp" maxLength="16"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('no_ktp',$data->no_ktp) : old('no_ktp') }}">
                <x-slot name="bottomSlot">
                    <small id="no_ktp-error" style="color: red"></small>
                </x-slot>
            </x-adminlte-input>
            <x-adminlte-input name="no_telepon" label="No. Telepon" placeholder="Tulis No. Telepon" id="no_telepon" maxLength="15"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('no_telepon',$data->no_telepon) : old('no_telepon') }}"/>
            <x-adminlte-input name="pekerjaan" label="Pekerjaan" placeholder="Tulis Pekerjaan" id="pekerjaan"
                fgroup-class="col-md-4" disable-feedback enable-old-support error-key value="{{ $data ? old('pekerjaan',$data->pekerjaan) : old('pekerjaan') }}"/>
        </div>
        <div class="row">
            <x-adminlte-textarea name="alamat" label="Alamat" placeholder="Tulis Alamat" id="alamat"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key>
                {{ $data ? old('alamat',$data->alamat) : old('alamat') }}
            </x-adminlte-textarea>
        </div>
    </form>
    @can('anggota-view')
    <a href="{{ route('anggota.main') }}" class="btn btn-default btn-flat">Kembali</a>
    @endcan
    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
    @can('anggota-delete')
    <a href="{{ route('anggota.nonaktif',['id'=>$data->id_anggota]) }}" class="btn btn-danger btn-flat">Non-Aktifkan</a>
    @endcan
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
        $('#no_ktp').keyup(function () {
            validationNoKTP()
        })
        function validationNoKTP(){
            $('#no_ktp').val($('#no_ktp').val().replace(/[^\d]/g, ''))

            let noKtp = $('#no_ktp').val()

            if(noKtp.length === 16){
                $('#no_ktp-error').html('')
                return true
            }else{
                $('#no_ktp-error').html('no ktp tidak boleh kurang dari 16 digit')
                return false
            }
        }

        $('#no_telepon').keyup(function () {
            $(this).val($(this).val().replace(/[^\d]/g, ''))
        })
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            if(!validationNoKTP()){
                return Swal.fire('Peringatan','no ktp tidak sesuai','warning')
            }
            const data = $('#formData').serialize()
            $.post("{{ route('anggota.store') }}", data)
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
