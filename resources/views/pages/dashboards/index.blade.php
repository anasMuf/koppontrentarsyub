@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Dashboard')
@section('content_header_title', 'Dashboard')
@section('plugins.Chartjs', true)

{{-- Content body: main page content --}}

@php($penjualanBulanIni)

@section('content_body')
<div class="row">
    <div class="col-md-3">
        <x-adminlte-small-box title="Pengurus" text="{{ $pengurusCount }} Orang" icon="fas fa-users"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="Anggota" text="{{ $anggotaCount }} Orang" icon="fas fa-id-card"/>
    </div>
    <div class="col-md-6">
        <x-adminlte-small-box title="Rp {{ number_format($penjualanBulanIni,0,',','.') }} - Pendapatan" text="Rp {{ number_format($labaBulanIni,0,',','.') }} - Laba" icon="fas fa-hand-holding-usd"/>
    </div>
    <div class="col-md-6">
        <x-adminlte-card title="Anggota Belum Terdaftar">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pendaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftaranAnggota as $item)
                    <tr>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>
                            <a href="{{ route('pendaftaran_anggota.form',['id'=>$item->id_pendaftaran_anggota]) }}" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center">Tidak ada data </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-slot name="footerSlot">
                <div class="d-flex justify-content-center">
                    <a href="{{ route('pendaftaran_anggota.main') }}">Selengkapnya</a>
                </div>
            </x-slot>
        </x-adminlte-card>
    </div>
    <div class="col-md-6">
        <x-adminlte-card title="Tunggakan Iuran Anggota Bulan Sekarang ({{ \Carbon\Carbon::parse(date('Y-m-d'))->isoFormat('MMMM') }})">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($iuranAnggotaTunggakan as $item)
                    <tr>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>
                            <a href="{{ route('iuran_anggota.payment', ['anggota' => $item->id_anggota,'periode_iuran'=>date('Y-m')]) }}" class="btn btn-warning btn-xs mx-1" title="Payment">
                                <i class="fa fa-lg fa-fw fa-coins"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center">Tidak ada data </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-slot name="footerSlot">
                <div class="d-flex justify-content-center">
                    <a href="{{ route('iuran_anggota.main') }}">Selengkapnya</a>
                </div>
            </x-slot>
        </x-adminlte-card>
    </div>
    <div class="col-md-6">
        <x-adminlte-card title="Stok Produk yang habis">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produkStokHabis as $item)
                    <tr>
                        <td>{{ $item->nama_produk_varian }}</td>
                        <td>
                            <a href="{{ route('produk.form', ['id' => $item->produk->id_produk]) }}" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center">Tidak ada data </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-slot name="footerSlot">
                <div class="d-flex justify-content-center">
                    <a href="{{ route('produk.main') }}">Selengkapnya</a>
                </div>
            </x-slot>
        </x-adminlte-card>
    </div>
    <div class="col-md-6">
        <x-adminlte-card title="Pembelian yang belum dikonfirmasi">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Pembelian</th>
                        <th>No Faktur</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembelianProses as $item)
                    <tr>
                        <td>{{ $item->tanggal_pembelian }}</td>
                        <td>{{ $item->no_faktur??'-' }}</td>
                        <td>{{ $item->supplier->nama_supplier }}</td>
                        <td>Rp {{ number_format($item->total_pembelian,0,',','.') }}</td>
                        <td>
                            <a href="{{ route('pembelian.form', ['id' => $item->id_pembelian]) }}" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center">Tidak ada data </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-slot name="footerSlot">
                <div class="d-flex justify-content-center">
                    <a href="{{ route('pembelian.main') }}">Selengkapnya</a>
                </div>
            </x-slot>
        </x-adminlte-card>
    </div>
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
    </script>
@endpush
