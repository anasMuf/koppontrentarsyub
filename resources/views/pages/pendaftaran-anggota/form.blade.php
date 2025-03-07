@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pendaftaran Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pendaftaran Anggota')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pendaftaran Anggota">
    <x-slot name="toolsSlot">
        @if ($data && $data->status_pendaftaran == 'proses')
            <x-adminlte-button class="btn-flat" type="button" label="Terima" theme="primary" icon="fas fa-check" id="approveButton" onclick="updateStatus('terima',$('#id_pendaftaran_anggota').val())"/>
            <x-adminlte-button class="btn-flat" type="button" label="Tolak" theme="danger" icon="fas fa-times" id="cencelButton" onclick="updateStatus('tolak',$('#id_pendaftaran_anggota').val())"/>
        @endif
    </x-slot>
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_pendaftaran_anggota" id="id_pendaftaran_anggota" value="{{ $data ? old('id_pendaftaran_anggota',$data->id_pendaftaran_anggota) : old('id_pendaftaran_anggota') }}">
        <div class="row">
            <x-adminlte-input name="no_formulir" label="No Formulir" placeholder="Tulis No Formulir" id="no_formulir" readonly
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('no_formulir',$data->no_formulir) : old('no_formulir',$no_formulir) }}"/>
            <x-adminlte-input type="date" name="tgl_pendaftaran" label="Tanggal Pendaftaran" placeholder="Tulis Tanggal Pendaftaran" id="tgl_pendaftaran"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tgl_pendaftaran',$data->tgl_pendaftaran) : old('tgl_pendaftaran',date('Y-m-d')) }}"/>
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
        @if ($data)
        {{-- <div class="row">
            <x-adminlte-select name="status_pendaftaran" label="Status Pendaftaran" fgroup-class="col-md-12">
                <x-adminlte-options :options="[
                    [
                        'text' => 'Proses',
                        'value' => 'proses',
                    ],
                    [
                        'text' => 'Tolak',
                        'value' => 'tolak',
                    ],
                    [
                        'text' => 'Terima',
                        'value' => 'terima',
                    ]
                ]"
                selected="{{ $data ? old('status_pendaftaran',$data->status_pendaftaran) : old('status_pendaftaran') }}" empty-option=".:: Pilih Status Pendafatarn ::."/>
            </x-adminlte-select>
        </div> --}}
        @endif
    </form>
    @can('pendaftaran_anggota-view')
    <a href="{{ route('pendaftaran_anggota.main') }}" class="btn btn-default btn-flat">Kembali</a>
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
            $.post("{{ route('pendaftaran_anggota.store') }}", data)
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
                        location.href = "{{ route('pendaftaran_anggota.form') }}"
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

        function updateStatus(status_pendaftaran,id) {
            let status
            if(status_pendaftaran === 'terima'){
                status = 'diterima'
            }else{
                status = 'ditolak'
            }
            Swal.fire({
                title: "Konfirmasi",
                text: `Apakah Anda yakin data pendaftaran ini ${status}?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then(function(result) {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "post",
                        url: "{{ route('pendaftaran_anggota.updateStatus') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id_pendaftaran_anggota": id,
                            "status_pendaftaran": status_pendaftaran
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                let timer = 1500
                                Swal.fire({
                                    title: "Sukses",
                                    text: response.message,
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: timer
                                });
                                setTimeout(() => {
                                    location.href = "{{ route('pendaftaran_anggota.main') }}"
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
                }
            });
        }
    </script>
@endpush
