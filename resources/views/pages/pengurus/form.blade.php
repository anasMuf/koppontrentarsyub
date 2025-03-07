@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pengurus')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pengurus')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pengurus">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_pengurus" value="{{ $data ? old('id_pengurus',$data->id_pengurus) : old('id_pengurus') }}">
        <input type="hidden" name="user_id" value="{{ $data ? old('user_id',$data->user_id) : old('user_id') }}">
        <div class="row">
            <x-adminlte-input name="nama_lengkap" label="Nama Lengkap" placeholder="Tulis Nama Lengkap" id="nama_lengkap"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_lengkap',$data->nama_lengkap) : old('nama_lengkap') }}"/>
        </div>
        <hr>
        <div class="row">
            <x-adminlte-input name="username" label="Username" placeholder="Tulis Username" id="username"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('username',$data->user->username) : old('username') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input type="email" name="email" label="Email" placeholder="Tulis Email" id="email"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('email',$data->user->email) : old('email') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input type="password" autocomplete="new-password" name="password" label="Password" placeholder="Tulis Password" id="password"
            fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ old('password') }}"/>
            <x-adminlte-input type="password" autocomplete="new-password" name="confirm_password" label="Ulangi Password" placeholder="Tulis Ulangi Password" id="confirm_password"
            fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ old('confirm_password') }}">
                <x-slot name="bottomSlot">
                    <small id="confirm-password-error" style="color: red"></small>
                </x-slot>
            </x-adminlte-input>
        </div>
        {{-- <div class="row">
            <x-adminlte-select name="selBasic" label="Level" fgroup-class="col-md-12">
                <x-adminlte-options :options="['Option 1', 'Option 2', 'Option 3']"
                selected="1" empty-option="Select an option..."/>
            </x-adminlte-select>
        </div> --}}
    </form>
    <a href="{{ route('pengurus.main') }}" class="btn btn-default btn-flat">Kembali</a>
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
            const data = $('#formData').serialize()
            $.post("{{ route('pengurus.store') }}", data)
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
                        location.href = "{{ route('pengurus.main') }}"
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
