@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pembayaran Iuran Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pembayaran Iuran Anggota')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Pembayaran Iuran Anggota Bulan {{ $bulan }}">
    <form method="post" id="formData">
        @csrf
        <input type="hidden" name="id_iuran_anggota" id="id_iuran_anggota" value="{{ $data ? old('id_iuran_anggota',$data->id_iuran_anggota) : old('id_iuran_anggota') }}">
        <input type="hidden" name="anggota_id" id="anggota_id" value="{{ old('anggota_id',$anggota->id_anggota) }}">
        <input type="hidden" name="jenis_iuran_id" id="jenis_iuran_id" value="{{ old('jenis_iuran_id',$jenis_iuran->id_jenis_iuran) }}">
        <div class="detail">
            <table>
                <tr>
                    <td>Transaksi</td>
                    <td>:</td>
                    <td>IURAN {{ strtoupper($jenis_iuran->jenis_iuran) }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $anggota->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>Tgl Bergabung</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($anggota->tgl_bergabung)->isoFormat('DD MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $anggota->alamat }}</td>
                </tr>
                <tr>
                    <td>No. Telepon</td>
                    <td>:</td>
                    <td>{{ $anggota->no_telepon }}</td>
                </tr>
                @if ($periode_iuran !== 'wajib')
                    <tr>
                        <td>Periode Tagihan</td>
                        <td>:</td>
                        <th>
                            {{ \Carbon\Carbon::parse($periode_iuran)->isoFormat('MMMM YYYY') }}
                            <input type="hidden" name="periode_iuran" id="periode_iuran" value="{{ $data ? old('periode_iuran',$periode_iuran) : old('periode_iuran',$periode_iuran) }}">
                        </th>
                    </tr>
                @endif
                <tr>
                    <td>Total Tagihan</td>
                    <td>:</td>
                    <th>
                        {{ number_format($jenis_iuran->nominal,0,',','.') }}
                        <input type="hidden" name="nominalFormated" id="nominalFormated" value="{{ $data ? old('nominalFormated',number_format($jenis_iuran->nominal,0,',','.')) : old('nominalFormated',number_format($jenis_iuran->nominal,0,',','.')) }}">
                    </th>
                </tr>

                @if ($data)
                <tr>
                    <td>Status Pembayaran</td>
                    <td>:</td>
                    <th>
                        @if ($data->status_pembayaran == 'sudah_bayar')
                        <span style="color: green">Sudah Dibayarkan</span>
                        @else
                        <span style="color: red;">Belum Dibayarkan</span>
                        @endif
                    </th>
                </tr>
                @endif
            </table>
        </div>
    </form>
    @if ($data)
    <a href="{{ route('iuran_anggota.print',['anggota'=>$anggota->id_anggota,'periode_iuran'=>$periode_iuran]) }}" class="btn btn-flat btn-warning btn-block" id="printButton" target="_blank">Cetak</a>
    @else
    <x-adminlte-button form="formData" class="btn-flat btn-block" type="submit" label="Bayar" theme="success" icon="fas fa-lg fa-card" id="submitButton"/>
    @endif
    @can('iuran_anggota-view')
    <a href="{{ route('iuran_anggota.detail',['anggota'=>$anggota->id_anggota]) }}" class="btn btn-default btn-flat btn-block">Kembali</a>
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
        let nominalFormated,nominalInt
        nominalInt = $('#nominalFormated').val().replace(/\D/g, '')

        $('#nominalFormated').keyup(function () {
            currencyEventHandler()
        })
        function currencyEventHandler(){
            nominalInt = $('#nominalFormated').val().replace(/\D/g, '')
            nominalFormated = $('#nominalFormated').val($('#nominalFormated').val().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
            return nominalFormated
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = serializeWithExtra('formData',{nominal: nominalInt})
            $.post("{{ route('iuran_anggota.store') }}", data)
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
                        location.reload()
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
