@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@php($nama = \App\Models\Pengaturan::where('setting','nama')->first()->value ?? '')
@php($logo = \App\Models\Pengaturan::where('setting','logo')->first()->value ?? '')

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand logo-switch {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link logo-switch {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
    <img src="{{ $logo == '' ? asset(config('adminlte.logo_img')) : asset('storage/images/logos/'.$logo) }}"
         alt="{{ $logo == '' ? asset(config('adminlte.logo_img_alt')) : $nama.' Logo' }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image-xl') }} logo-xs">

    {{-- Large brand logo --}}
    <img src="{{ $logo == '' ? asset(config('adminlte.logo_img_xl')) : asset('storage/images/logos/'.$logo) }}"
         alt="{{ $logo == '' ? asset(config('adminlte.logo_img_alt')) : $nama.' Logo' }}"
         class="{{ config('adminlte.logo_img_xl_class', 'brand-image-xs') }} logo-xl">

</a>
