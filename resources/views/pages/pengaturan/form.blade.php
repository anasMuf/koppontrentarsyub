@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pengaturan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pengaturan')
@section('plugins.BsCustomFileInput', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pengaturan">
    <form action="" method="post" id="formData" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_pengaturan" value="{{ $data ? old('id_pengaturan',$data->id_pengaturan) : old('id_pengaturan') }}">
        <div class="row">
            <x-adminlte-input name="setting" label="Nama Pengaturan" placeholder="Tulis Nama Pengaturan" id="setting" readonly
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('setting',$data->setting) : old('setting') }}"/>
            @if($data->setting === 'alamat')
            <x-adminlte-textarea name="value" label="Value Pengaturan" placeholder="Tulis Value Pengaturan" id="value"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key>
                {{ $data ? old('value',$data->value) : old('value') }}
            </x-adminlte-textarea>
            @elseif($data->setting === 'logo')
            <x-adminlte-input-file name="value_file" label="Upload file logo" placeholder="Pilih file logo..."
                fgroup-class="col-md-6" accept="image/*" disable-feedback/>
            @else
            <x-adminlte-input type="value" name="value" label="Value Pengaturan" placeholder="Tulis Value Pengaturan" id="value"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('value',$data->value) : old('value') }}"/>
            @endif
        </div>
    </form>
    <a href="{{ route('pengaturan.main') }}" class="btn btn-default btn-flat">Kembali</a>
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
        $('#password, #confirm_password').on('keyup', function() {
            validatePassword()
        });

        function validatePassword() {
            const password = $('#password').val()
            const confirmPassword = $('#confirm_password').val()

            $('#confirm-password-error').text('');

            if (password !== confirmPassword) {
                $('#confirm-password-error').text('Password tidak cocok');
                return false;
            }

            return true;
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            if(!validatePassword()){
                return Swal.fire('Peringatan','password tidak sama','warning')
            }
            const data = new FormData(this);

            $.ajax({
                url: "{{ route('pengaturan.store') }}",
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
                            location.href = "{{ route('pengaturan.main') }}"
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
