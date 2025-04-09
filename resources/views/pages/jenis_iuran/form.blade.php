@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Jenis Iuran')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Jenis Iuran')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Jenis Iuran">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_jenis_iuran" id="id_jenis_iuran" value="{{ $data ? old('id_jenis_iuran',$data->id_jenis_iuran) : old('id_jenis_iuran') }}">
        <div class="row">
            <x-adminlte-select name="jenis_iuran" label="Jenis Iuran" fgroup-class="col-md-6">
                <x-adminlte-options :options="[
                    [
                        'text' => 'Wajib',
                        'value' => 'wajib',
                    ],
                    [
                        'text' => 'Pokok',
                        'value' => 'pokok',
                    ],
                    [
                        'text' => 'Sukarela',
                        'value' => 'sukarela',
                    ]
                ]"
                selected="{{ $data ? old('jenis_iuran',$data->jenis_iuran) : old('jenis_iuran') }}" empty-option=".:: Pilih Status Pendafatarn ::."/>
            </x-adminlte-select>
            <x-adminlte-input name="nominalFormated" label="Nominal" placeholder="Tulis Nominal" id="nominalFormated" maxLength="20"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nominalFormated',number_format($data->nominal,0,',','.')) : old('nominalFormated') }}"/>
        </div>
        <div class="row">
            <x-adminlte-textarea name="keterangan" label="Keterangan" placeholder="Tulis Keterangan" id="keterangan"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key>
                {{ $data ? old('keterangan',$data->keterangan) : old('keterangan') }}
            </x-adminlte-textarea>
        </div>
    </form>
    @can('jenis_iuran-view')
    <a href="{{ route('jenis_iuran.main') }}" class="btn btn-default btn-flat">Kembali</a>
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
        let nominalFormated,nominalInt
        $('#nominalFormated').keyup(function () {
            currencyEventHandler()
        })
        function currencyEventHandler(){
            nominalInt = $('#nominalFormated').val().replace(/\D/g, '')
            nominalFormated = $('#nominalFormated').val($('#nominalFormated').val().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
            return nominalFormated
        }

        $(document).ready(function () {
            $('#nominalFormated').closest('.form-group').hide()
        });

        $('#jenis_iuran').change(function () {
            if($(this).val() == 'sukarela'){
                $('#nominalFormated').val('')
                $('#nominalFormated').closest('.form-group').hide()
            }else{
                $('#nominalFormated').closest('.form-group').show()
            }
        })
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = serializeWithExtra('formData',{nominal: nominalInt})
            $.post("{{ route('jenis_iuran.store') }}", data)
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
                        location.href = "{{ route('jenis_iuran.main') }}"
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
