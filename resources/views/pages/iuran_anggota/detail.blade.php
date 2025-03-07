@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Detail Iuran Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Detail Iuran Anggota')

{{-- Content body: main page content --}}

@section('content_body')
@can('iuran_anggota-view')
<a href="{{ route('iuran_anggota.main') }}" class="btn btn-default btn-flat mb-1">Kembali</a>
@endcan
<x-adminlte-card title="Profile Anggota">
    <div class="detail">
        <table>
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
        </table>
    </div>
</x-adminlte-card>

<x-adminlte-card title="Data Iuran Wajib">
    <x-adminlte-datatable id="table1" :heads="$heads1" :config="$config1">
        @foreach($config1['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>
</x-adminlte-card>

<x-adminlte-card title="Data Periode Iuran Anggota">
    @can('iuran_anggota-create')
    <div class="action">
    </div>
    @endcan
    <x-adminlte-datatable id="table2" :heads="$heads2" :config="$config2">
        @foreach($config2['data'] as $row)
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
    </script>
@endpush
