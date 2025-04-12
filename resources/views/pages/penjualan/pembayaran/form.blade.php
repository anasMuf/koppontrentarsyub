@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Riwayat Pembayaran')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Riwayat Pembayaran')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Riwayat Pembayaran">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="penjualan_id" id="penjualan_id" value="{{ $penjualan->id_penjualan }}">
        <input type="hidden" name="id_penjualan_pembayaran" id="id_penjualan_pembayaran" value="{{ $data ? old('id_penjualan_pembayaran',$data->id_penjualan_pembayaran) : old('id_penjualan_pembayaran') }}">
        {{-- <div class="row">
            <x-adminlte-input name="nama_lengkap" label="Nama Lengkap" placeholder="Tulis Nama Lengkap" id="nama_lengkap"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_lengkap',$data->nama_lengkap) : old('nama_lengkap') }}"/>
        </div> --}}
        {{-- <div class="row"> --}}
            <div class="form-group">
                <label>Total Sisa</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control" id="sisa_total" name="sisa_total" value="{{ $penjualan->total_penjualan-$penjualan->nominal_dibayar }}" readonly>
                    <input type="hidden" id="sisa_total_clean" name="sisa_total_clean" value="{{ $penjualan->total_penjualan-$penjualan->nominal_dibayar }}" disabled>
                </div>
            </div>
        {{-- </div> --}}
        {{-- <div class="row"> --}}
            <div class="form-group">
                <label>Jumlah Bayar</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control" id="amount_paid" name="amount_paid">
                </div>
            </div>
        {{-- </div> --}}
        {{-- <div class="row"> --}}
            <div class="form-group">
                <label>Sisa</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control" id="change_amount" name="change_amount" readonly>
                </div>
            </div>
        {{-- </div> --}}
    </form>
    @can('penjualan-view')
    <a href="{{ route('pembayaran_penjualan.main',['penjualan'=>$penjualan->id_penjualan]) }}" class="btn btn-default btn-flat">Kembali</a>
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
        $(document).ready(function () {
            $('#sisa_total').val(formatRupiah($('#sisa_total').val()));
            if($('#id_penjualan_pembayaran').val() !== ''){
                calculatePayment()
            }
        });
        $('#amount_paid').keyup(function () {
            calculatePayment()
        })
        function calculatePayment(){

            let sisa = unformatCurrency($('#sisa_total_clean').val()) || 0;
            let amountPaid = unformatCurrency($('#amount_paid').val()) || 0;
            let changeAmount = sisa - amountPaid;

            $('#change_amount').val(formatRupiah(changeAmount > 0 ? changeAmount : 0));
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const penjualanId = $('#penjualan_id').val();
            const data = $('#formData').serialize()
            $.post("/pembayaran-penjualan/"+penjualanId+"/store", data)
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
                        location.href = "/pembayaran-penjualan/"+penjualanId
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
