<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $admin = Role::create([
        //     'name' => 'admin',
        // ]);

        // $pengurus = Role::create([
        //     'name' => 'pengurus',
        // ]);

        // $anggota = Role::create([
        //     'name' => 'anggota',
        // ]);

        $admin = Role::where([
            'name' => 'admin',
        ])->first();

        $pengurus = Role::where([
            'name' => 'pengurus',
        ])->first();

        $anggota = Role::where([
            'name' => 'anggota',
        ])->first();

        // Buat permissions
        $permissions = [
            // 'users-view',
            // 'users-create',
            // 'users-edit',
            // 'users-delete',

            // 'anggota-view',
            // 'anggota-create',
            // 'anggota-edit',
            // 'anggota-delete',

            // 'pengurus-view',
            // 'pengurus-create',
            // 'pengurus-edit',
            // 'pengurus-delete',

            // 'pendaftaran_anggota-view',
            // 'pendaftaran_anggota-create',
            // 'pendaftaran_anggota-edit',
            // 'pendaftaran_anggota-delete',

            // 'jenis_iuran-view',
            // 'jenis_iuran-create',
            // 'jenis_iuran-edit',
            // 'jenis_iuran-delete',

            // 'iuran_anggota-view',
            // 'iuran_anggota-create',
            // 'iuran_anggota-edit',
            // 'iuran_anggota-delete',

            // 'pengaturan-view',
            // 'pengaturan-create',
            // 'pengaturan-edit',
            // 'pengaturan-delete',

            // 'kategori_produk-view',
            // 'kategori_produk-create',
            // 'kategori_produk-edit',
            // 'kategori_produk-delete',

            // 'produk-view',
            // 'produk-create',
            // 'produk-edit',
            // 'produk-delete',

            // 'supplier-view',
            // 'supplier-create',
            // 'supplier-edit',
            // 'supplier-delete',

            // 'pembelian-view',
            // 'pembelian-create',
            // 'pembelian-edit',
            // 'pembelian-delete',

            'penjualan-view',
            'penjualan-create',
            'penjualan-edit',
            'penjualan-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions ke roles
        $admin->givePermissionTo(Permission::all());
        $pengurus->givePermissionTo([
            // 'users-view','users-create','users-edit','users-delete',
            // 'anggota-view','anggota-create','anggota-edit','anggota-delete',
            // 'pengurus-view','pengurus-edit',
            // 'pendaftaran_anggota-view','pendaftaran_anggota-create','pendaftaran_anggota-edit','pendaftaran_anggota-delete',
            // 'jenis_iuran-view','jenis_iuran-create','jenis_iuran-edit','jenis_iuran-delete',
            // 'iuran_anggota-view','iuran_anggota-create','iuran_anggota-edit','iuran_anggota-delete',
        ]);
        $anggota->givePermissionTo([
            // 'anggota-edit',
            // 'pendaftaran_anggota-create',
        ]);
    }
}
