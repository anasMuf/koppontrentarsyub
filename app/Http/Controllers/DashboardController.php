<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\IuranAnggota;
use App\Models\Pembelian;
use App\Models\PendaftaranAnggota;
use App\Models\Pengurus;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\ProdukVarian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pengurusCount = Pengurus::count();
        $anggotaCount = Anggota::count();
        $pendaftaranAnggota = PendaftaranAnggota::where('status_pendaftaran','proses')->limit(3)->get();

        $produkStokHabis = ProdukVarian::with(['produk'])->where('stok_sekarang',0)->limit(3)->get();
        $pembelianProses = Pembelian::with(['supplier','pembelian_detail.produk_varian.produk'])->where('status_pembelian','proses')->limit(3)->get();
        $penjualanBulanIni = Penjualan::selectRaw('sum(total_penjualan) as penjualanBulanIni')->whereMonth('tanggal_penjualan',date('m'))->whereYear('tanggal_penjualan',date('Y'))->first()->penjualanBulanIni;

        $labaBulanIni = PenjualanDetail::join('produk_varian', 'penjualan_detail.produk_varian_id', '=', 'produk_varian.id_produk_varian')
        ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id_penjualan')
        ->whereMonth('penjualan.tanggal_penjualan', date('m'))
        ->whereYear('penjualan.tanggal_penjualan', date('Y'))
        ->selectRaw('SUM((penjualan_detail.harga_satuan - produk_varian.harga_beli) * penjualan_detail.qty) as total_laba')
        ->first()
        ->total_laba;


        $iuranAnggotaTunggakan = Anggota::with(['iuran_anggota' => function($qia){
            $qia->where('jenis_iuran_id',1)
            ->whereMonth('periode_iuran',date('m'))
            ->whereYear('periode_iuran',date('Y'));
        }])
        ->limit(3)
        ->get()
        ->filter(function($anggota) {
            return $anggota->iuran_anggota->isEmpty();
        });

        return view('pages.dashboards.index',compact(
            'pengurusCount',
            'anggotaCount',
            'pendaftaranAnggota',
            'iuranAnggotaTunggakan',
            'produkStokHabis',
            'pembelianProses',
            'penjualanBulanIni',
            'labaBulanIni',
        ));
    }
}
