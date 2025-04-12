@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', "Riwayat Pembayaran")
@section('content_header_title', 'Home')
@section('content_header_subtitle', "Riwayat Pembayaran")

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Riwayat Pembayaran {{ $penjualan->no_struk??'-' }}">
    @can('penjualan-view')
    <a href="{{ route('penjualan.form',['id'=>$penjualan->id_penjualan]) }}" class="btn btn-default btn-flat">Kembali</a>
    @endcan
    <input type="hidden" name="penjualan_id" id="penjualan_id" value="{{ $penjualan->id_penjualan }}">
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
                    <td>{{ $penjualan->no_struk??'-' }}</td>
                </tr>
                <tr>
                    <td>Tgl Transaksi</td>
                    <td>:</td>
                    <td>{{ $penjualan->tanggal_penjualan }}</td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>:</td>
                    <td>{{ $penjualan->metode_pembayaran }}</td>
                </tr>
                <tr>
                    <td>Catatan</td>
                    <td>:</td>
                    <td>{{ $penjualan->catatan??'-' }}</td>
                </tr>
                <tr>
                    <td>Nama Anggota</td>
                    <td>:</td>
                    <td>{{ $penjualan->anggota->nama_lengkap ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Total Nominal</td>
                    <td>:</td>
                    <td>Rp {{ number_format($penjualan->total_penjualan,0,',','.') }}</td>
                </tr>
                <tr>
                    <td>Nominal Dibayar</td>
                    <td>:</td>
                    <td>Rp {{ number_format($penjualan->nominal_dibayar,0,',','.') }}</td>
                </tr>
                <tr>
                    <td>Nominal Belum Dibayar</td>
                    <td>:</td>
                    <td>Rp {{ number_format($penjualan->total_penjualan-$penjualan->nominal_dibayar,0,',','.') }}</td>
                </tr>
            </table>
        </div>
    </div>
    @can('penjualan-create')
    <div class="action">
        @if($penjualan->status_pembayaran == 'Belum Lunas')
        <a href="{{ route('pembayaran_penjualan.form',['penjualan'=>$penjualan->id_penjualan]) }}" class="btn btn-primary">Tambah</a>
        @else
        <button type="button" class="btn btn-success">Lunas</button>
        @endif
    </div>
    @endcan
    <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
        @foreach($config['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>
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
        function deleteData(id,nama){
            const penjualanId = $('#penjualan_id').val();
            Swal.fire({
                title: "Data akan dihapus!",
                text: "Apakah Anda yakin data "+nama+" dihapus?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Tidak',
                confirmButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "delete",
                        url: "/pembayaran-penjualan/"+penjualanId+"/delete/"+id,
                        data: {
                            "_token": "{{ csrf_token() }}"
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
                                    location.href = "/pembayaran-penjualan/"+penjualanId
                                }, timer);
                            }else{
                                Swal.fire('Peringatan', response.message, 'warning')
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
