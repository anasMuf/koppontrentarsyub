@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@php($nama = \App\Models\Pengaturan::where('setting','nama')->first()->value ?? '')
@php($logo = \App\Models\Pengaturan::where('setting','logo')->first()->value ?? '')

@section('title')
    {{ $nama == '' ? config('adminlte.title') :$nama }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '/') }}">
            {{ $nama == '' ? config('app.company_name', 'My company') : $nama }}
        </a>
    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}

@push('js')
<script src="{{ asset('dist/css/js/script.js') }}"></script>
<script>

    $(document).ready(function() {
        // Add your common script logic here...
    });

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi untuk menghapus format mata uang dan mendapatkan nilai numerik
    function unformatCurrency(currency) {
        return parseFloat(currency.replace(/[^\d.-]/g, '')) || 0;
    }

    // pembulatan
    function round(value, precision) {
        var multiplier = Math.pow(10, precision || 0);
        return Math.round(value * multiplier) / multiplier;
    }
</script>
@endpush

{{-- Add common CSS customizations --}}

@push('css')
<style type="text/css">

    {{-- You can add AdminLTE customizations here --}}
    /*
    .card-header {
        border-bottom: none;
    }
    .card-title {
        font-weight: 600;
    }
    */

</style>
@endpush
