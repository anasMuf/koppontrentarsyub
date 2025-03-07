@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php($nama = \App\Models\Pengaturan::where('setting','nama')->first()->value ?? '')
@php($logo = \App\Models\Pengaturan::where('setting','logo')->first()->value ?? '')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
    <img src="{{ $logo == '' ? asset(config('adminlte.logo_img')) : asset('dist/images/logos/'.$logo) }}"
         alt="{{ $logo == '' ? asset(config('adminlte.logo_img_alt')) : $nama.' Logo' }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-3') }}"
         style="opacity:.8">

    {{-- Brand text --}}
    <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
        @if ($nama)
        <b>{!! $nama !!}</b>
        @else
        {!! config('adminlte.logo') !!}
        @endif
    </span>

</a>
