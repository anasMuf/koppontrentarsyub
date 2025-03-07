@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Iuran Anggota')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Iuran Anggota')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Iuran Anggota">
    @can('iuran_anggota-create')
    <div class="action">
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
    </script>
@endpush
