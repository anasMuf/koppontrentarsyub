<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use NumberFormatter;
use App\Models\Produk;
use App\Models\Anggota;
use App\Models\Penjualan;
use App\Helpers\LogPretty;
use App\Models\Pengaturan;
use App\Models\ProdukVarian;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;
use App\Models\PenjualanPembayaran;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'No Struk',
            'Tanggal Penjualan',
            'Total',
            ['label' => 'Status', 'width' => 15],
            ['label' => 'Pembayaran', 'width' => 15],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $penjualan =  Penjualan::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, ['orderable' => false], null, null, null, null, ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $status = '';
        $statusPembayaran = '';
        $no = 1;

        foreach($penjualan as $item){
            if (auth()->user()->can('penjualan-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_penjualan.',`'.$item->penjualan.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('penjualan-edit')) {
                $btnDetails = '<a href="'.route('penjualan.form', ['id' => $item->id_penjualan]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $format = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $format->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

            if($item->status_penjualan === 'berhasil'){
                $status = '<span class="badge badge-success">Berhasil</span>';
            }elseif($item->status_penjualan === 'gagal'){
                $status = '<span class="badge badge-danger">Gagal</span>';
            }else{
                $status = '<span class="badge badge-light">Proses</span>';
            }

            if($item->status_pembayaran == 'Lunas'){
                $statusPembayaran = '<span class="badge badge-success">Lunas</span>';
            }else{
                $statusPembayaran = '<span class="badge badge-danger">Belum Lunas</span>';
            }

            $data['config']['data'][] = [
                $no++,
                $item->no_struk??'-',
                Carbon::parse($item->tanggal_penjualan)->isoFormat('DD MMMM YYYY'),
                $format->formatCurrency($item->total_penjualan, 'IDR'),
                $status,
                $statusPembayaran,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.penjualan.index',$data);
    }

    public function pos(){
        $data['data'] = [];
        $data['anggota'] = array_map(function($item){
            return ['text' => $item['nama_lengkap'], 'value' => $item['id_anggota']];
        },Anggota::get()->toArray());
        $data['produk'] = Produk::with(['kategori_produk','produk_varian'])->get();
        $data['metode_pembayaran'] = [
            [
                'text' => 'Tunai',
                'value' => 'cash'
            ],
            // [
            //     'text' => 'Kartu Debit/Kredit',
            //     'value' => 'card'
            // ],
            [
                'text' => 'Transfer Bank',
                'value' => 'transfer'
            ],
            // [
            //     'text' => 'QRIS',
            //     'value' => 'qris'
            // ],
        ];
        return view('pages.penjualan.pos',$data);
    }

    public function form(Request $request){
        $data['data'] = Penjualan::with(['anggota','penjualan_detail.produk_varian.produk','penjualan_pembayaran'])->find($request->id);
        return view('pages.penjualan.form',$data);
    }

    public function store(Request $request) {
        $rules = [
            'no_struk' => 'nullable|unique:penjualan,no_struk',
            'anggota_id' => 'required|exists:anggota,id_anggota',
            'tgl_penjualan' => 'required|date',
            'grand_total' => 'required',
            'items' => 'required|array|min:1',
            'items.*.produk_varian_id' => 'required|exists:produk_varian,id_produk_varian',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'unique' => ':attribute sudah dipakai',
            'exists' => ':attribute tidak tersedia',
            'date' => ':attribute harus berupa tanggal',
            'numeric' => ':attribute harus berupa nominal uang',
            'array' => ':attribute harus berupa array',
            'min' => ':attribute tidak sesuai minimal nominal',
        ];
        $attributes = [
            'no_struk' => 'No Faktur',
            'anggota_id' => 'Anggota',
            'tgl_penjualan' => 'Tgl Penjualan',
            'grand_total' => 'Total',
            'items' => 'Keranjang',
            'items.*.produk_varian_id' => 'Produk Varian',
            'items.*.qty' => 'Jumlah',
            'items.*.harga_satuan' => 'Harga',
            'items.*.subtotal' => 'Subtotal',
        ];
        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $total_penjualan = 0;

            $penjualan = new Penjualan;
            $penjualan->anggota_id = $request->anggota_id;
            $penjualan->tanggal_penjualan = date('Y-m-d',strtotime($request->tgl_penjualan)).' '.date('H:i:s');
            $penjualan->no_struk = $request->no_struk;
            $penjualan->total_penjualan = $total_penjualan;
            $penjualan->metode_pembayaran = $request->metode_pembayaran??null;
            $penjualan->status_penjualan = isset($request->status_penjualan) ? $request->status_penjualan : 'berhasil';
            $penjualan->catatan = $request->catatan??null;
            $penjualan->save();

            foreach ($request->items as $item) {
                $sub_total = $item['harga_satuan'] * $item['qty'];

                $penjualan_detail = new PenjualanDetail;
                $penjualan_detail->penjualan_id = $penjualan->id_penjualan;
                $penjualan_detail->produk_varian_id = $item['produk_varian_id'];
                $penjualan_detail->qty = $item['qty'];
                $penjualan_detail->harga_satuan = $item['harga_satuan'];
                $penjualan_detail->sub_total = $sub_total;
                $penjualan_detail->save();

                $total_penjualan += $sub_total;

                // Update product stock quantity
                $produk_varian = ProdukVarian::find($item['produk_varian_id']);
                $produk_varian->stok_sekarang = $produk_varian->stok_sekarang - $item['qty'];
                $produk_varian->save();
            }

            $penjualan->total_penjualan = $total_penjualan;
            $penjualan->save();

            PenjualanPembayaran::create([
                'penjualan_id' => $penjualan->id_penjualan,
                'nominal' => $request->amount_paid,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th,$request->all());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, kesalahan pada sistem',
            ]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::findOrFail($id);
            PenjualanDetail::where('penjualan_id',$penjualan->id_penjualan)->delete();
            $penjualan->delete();


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data, ',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data, kesalahan pada sistem',
            ]);
        }
    }

    public function print(Request $request){
        $data['nama'] = Pengaturan::where('setting','nama')->first()->value ?? '';
        $data['logo'] = Pengaturan::where('setting','logo')->first()->value ?? '';
        $data['alamat'] = Pengaturan::where('setting','alamat')->first()->value ?? '';
        $data['telepon'] = Pengaturan::where('setting','telepon')->first()->value ?? '';
        $data['email'] = Pengaturan::where('setting','email')->first()->value ?? '';

        $data['data'] = Penjualan::with(['anggota','penjualan_detail.produk_varian.produk'])->find($request->id);
        return view('pages.penjualan.print',$data);
    }
}
