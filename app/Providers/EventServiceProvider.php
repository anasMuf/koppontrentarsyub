<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add([
                'text' => 'Pengurus',
                'route' => 'pengurus.main',
                'active' => ['pengurus', 'pengurus*', 'regex:@^pengurus/[0-9]+$@'],
                'icon' => 'fas fa-network-wired',
                'can' => 'pengurus-view'
            ]);
            $event->menu->add('Keanggotaan');
            $event->menu->add([
                'text' => 'Pendaftaran Anggota',
                'route' => 'pendaftaran_anggota.form',
                'active' => ['pendaftaran-anggota/form', 'pendaftaran-anggota/form*', 'regex:@^pendaftaran-anggota/form/[0-9]+$@'],
                'icon' => 'far fa-newspaper',
                'can' => 'pendaftaran_anggota-create'
            ]);
            $event->menu->add([
                'text' => 'Anggota',
                'icon' => 'far fa-id-card',
                'can' => ['anggota-view','pendaftaran_anggota-view'],
                'submenu' => [
                    [
                        'text' => 'Data Pendaftaran Anggota',
                        'route' => 'pendaftaran_anggota.main',
                        'active' => ['pendaftaran-anggota'],
                        'can' => 'pendaftaran_anggota-view',
                    ],
                    [
                        'text' => 'Data Anggota',
                        'route' => 'anggota.main',
                        'active' => ['anggota', 'anggota*', 'regex:@^anggota/[0-9]+$@'],
                        'can' => 'anggota-view',
                    ],
                ]
            ]);
            $event->menu->add([
                'text' => 'Jenis Iuran',
                'route' => 'jenis_iuran.main',
                'icon' => 'fas fa-sliders-h',
                'active' => ['jenis-iuran', 'jenis-iuran*', 'regex:@^jenis-iuran/[0-9]+$@'],
                'can' => 'jenis_iuran-view'
            ]);
            $event->menu->add([
                'text' => 'Iuran Anggota',
                'route' => 'iuran_anggota.main',
                'icon' => 'fas fa-coins',
                'active' => ['iuran-anggota', 'iuran-anggota*', 'regex:@^iuran-anggota/[0-9]+$@'],
                'can' => 'iuran_anggota-view'
            ]);
            $event->menu->add('Point of Sell');
            $event->menu->add([
                'text' => 'Kategori Produk',
                'route' => 'kategori_produk.main',
                'icon' => 'fas fa-tag',
                'active' => ['kategori-produk', 'kategori-produk*', 'regex:@^kategori-produk/[0-9]+$@'],
                'can' => 'kategori_produk-view'
            ]);
            $event->menu->add([
                'text' => 'Produk',
                'route' => 'produk.main',
                'icon' => 'fas fa-th-large',
                'active' => ['produk', 'produk*', 'regex:@^produk/[0-9]+$@'],
                'can' => 'produk-view'
            ]);
            $event->menu->add([
                'text' => 'Supplier',
                'route' => 'supplier.main',
                'icon' => 'fas fa-parachute-box',
                'active' => ['supplier', 'supplier*', 'regex:@^supplier/[0-9]+$@'],
                'can' => 'supplier-view'
            ]);
            $event->menu->add([
                'text' => 'Pembelian',
                'route' => 'pembelian.main',
                'icon' => 'fas fa-truck-loading',
                'active' => ['pembelian', 'pembelian*', 'regex:@^pembelian/[0-9]+$@'],
                'can' => 'pembelian-view'
            ]);
            $event->menu->add([
                'text' => 'Poin of Sell',
                'route' => 'penjualan.pos',
                'icon' => 'fas fa-cash-register',
                'active' => ['penjualan/pos', 'penjualan/pos*', 'regex:@^penjualan/pos/[0-9]+$@'],
                'can' => 'penjualan-create'
            ]);
            $event->menu->add([
                'text' => 'Penjualan',
                'route' => 'penjualan.main',
                'icon' => 'fas fa-receipt',
                'active' => ['penjualan', 'penjualan/form*', 'regex:@^penjualan/form[0-9]+$@'],
                'can' => 'penjualan-view'
            ]);
            $event->menu->add('Pengaturan');
            // $event->menu->add([
            //     'text' => 'Akun Pengguna',
            //     'url' => 'users',
            //     'icon' => 'fas fa-users',
            //     'can' => 'users-view',
            //     'submenu' => [
            //         [
            //             'text' => 'Akun Pengurus',
            //             'route' => 'akun_pengurus.main',
            //             'active' => ['akun-pengurus', 'akun-pengurus*', 'regex:@^akun-pengurus/[0-9]+$@'],
            //             'can' => 'akun_pengurus-view',
            //         ],
            //         [
            //             'text' => 'Data Anggota',
            //             'route' => 'akun_anggota.main',
            //             'active' => ['akun-anggota', 'akun-anggota*', 'regex:@^akun-anggota/[0-9]+$@'],
            //             'can' => 'akun_anggota-view',
            //         ],
            //     ]
            // ]);
            // $event->menu->add([
            //     'text' => 'Role',
            //     'url' => 'role',
            //     'icon' => 'fas fa-layer-group',
            //     'can' => 'users-view'
            // ]);
            $event->menu->add([
                'text' => 'Pengaturan',
                'route' => 'pengaturan.main',
                'icon' => 'fas fa-cogs',
                'active' => ['pengaturan', 'pengaturan*', 'regex:@^pengaturan/[0-9]+$@'],
                'can' => 'pengaturan-view'
            ]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
