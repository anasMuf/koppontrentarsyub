@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Detail Penjualan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Detail Penjualan')

{{-- Content body: main page content --}}

@section('content_body')
<x-adminlte-card title="Detail Penjualan">
    <div class="row">
        <div class="col-md-12 detail">
            <table>
                <tr>
                    <td>Transaksi</td>
                    <td>:</td>
                    <td>Penjualan</td>
                </tr>
                <tr>
                    <td>No Struk</td>
                    <td>:</td>
                    <td>{{ $data->no_struk??'-' }}</td>
                </tr>
                <tr>
                    <td>Tgl Transaksi</td>
                    <td>:</td>
                    <td>{{ $data->tanggal_penjualan }}</td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>:</td>
                    <td>{{ $data->metode_pembayaran }}</td>
                </tr>
                <tr>
                    <td>Catatan</td>
                    <td>:</td>
                    <td>{{ $data->catatan??'-' }}</td>
                </tr>
                <tr>
                    <td>Nama Anggota</td>
                    <td>:</td>
                    <td>{{ $data->anggota->nama_lengkap ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-pos-detail">
                <thead>
                    <tr>
                        <td>Produk</td>
                        <td>Harga</td>
                        <td>Jumlah</td>
                        <td>Sub Total</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->penjualan_detail as $item)
                    <tr>
                        <td>{{ $item->produk_varian->nama_produk_varian??$item->produk_varian->produk->nama_produk }}</td>
                        <td>
                            <div class="currency">
                                <span>Rp</span>
                                <span>{{ number_format($item->harga_satuan,0,',','.') }}</span>
                            </div>
                        </td>
                        <td class="number">{{ $item->qty }}</td>
                        <td>
                            <div class="currency">
                                <span>Rp</span>
                                <span>{{ number_format($item->sub_total,0,',','.') }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="number">Total</th>
                        <th>
                            <div class="currency">
                                <span>Rp</span>
                                <span>{{ number_format($data->total_penjualan,0,',','.') }}</span>
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <x-slot name="footerSlot">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('penjualan.main') }}" class="btn btn-default" id="btnKembaliData">Kembali ke Data Penjualan</a>
                <a href="{{ route('penjualan.pos') }}" class="btn btn-default" id="btnKembaliPOS">Kembali ke POS</a>
                <button type="button" class="btn btn-danger" id="btnHapus">
                    <i class="fas fa-times-circle"></i> Hapus
                </button>
                <a href="{{ route('penjualan.print',['id'=>$data->id_penjualan]) }}" target="_blank" class="btn btn-warning" id="btnPrint">
                    <i class="fas fa-print"></i> Print Struk
                </a>
            </div>
        </div>
    </x-slot>
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
        $(document).ready(function() {

        });
    </script>
    <script>
        $('#btnHapus').click(function() {
            Swal.fire({
                title: "Hapus Transaksi",
                text: "Apakah Anda yakin ingin menghapus transaksi ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Tidak',
                confirmButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('penjualan.pos') }}"; // Kembali ke halaman POS
                }
            });
        });
    </script>
@endpush
