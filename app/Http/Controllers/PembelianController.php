<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use NumberFormatter;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Helpers\LogPretty;
use App\Models\Pengaturan;
use App\Models\ProdukVarian;
use Illuminate\Http\Request;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'No Faktur',
            'Tanggal Pembelian',
            'Supplier',
            'Total',
            ['label' => 'Status', 'width' => 15],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pembelian =  Pembelian::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, ['orderable' => false], null, ['orderable' => false], null, null, ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $status = '';
        $no = 1;

        foreach($pembelian as $item){
            if (auth()->user()->can('pembelian-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_pembelian.',`'.$item->pembelian.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('pembelian-edit')) {
                $btnDetails = '<a href="'.route('pembelian.form', ['id' => $item->id_pembelian]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $format = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $format->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

            if($item->status_pembelian === 'berhasil'){
                $status = '<span class="badge badge-success">Berhasil</span>';
            }elseif($item->status_pembelian === 'gagal'){
                $status = '<span class="badge badge-danger">Gagal</span>';
            }else{
                $status = '<span class="badge badge-light">Proses</span>';
            }

            $data['config']['data'][] = [
                $no++,
                $item->no_faktur??'-',
                Carbon::parse($item->tanggal_pembelian)->isoFormat('DD MMMM YYYY'),
                $item->supplier->nama_supplier,
                $format->formatCurrency($item->total_pembelian, 'IDR'),
                $status,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pembelian.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Pembelian::with(['pembelian_detail.produk_varian.produk'])->find($request->id) : [];
        $data['supplier'] = array_map(function($item){
            return ['text' => $item['nama_supplier'], 'value' => $item['id_supplier']];
        },Supplier::get()->toArray());
        $data['status_pembelian'] = [
            [
                'text' => 'Proses',
                'value' => 'proses',
            ],
            [
                'text' => 'Berhasil',
                'value' => 'berhasil',
            ],
            [
                'text' => 'Gagal',
                'value' => 'gagal',
            ],
        ];
        return view('pages.pembelian.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'no_faktur' => 'nullable|unique:pembelian,no_faktur',
            'supplier_id' => 'required|exists:supplier,id_supplier',
            'tgl_pembelian' => 'required|date',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.produk_varian_id' => 'required|exists:produk_varian,id_produk_varian',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
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
            'no_faktur' => 'No Faktur',
            'supplier_id' => 'Supplier',
            'tgl_pembelian' => 'Tgl Pembelian',
            'total' => 'Total',
            'items.*.produk_varian_id' => 'Produk Varian',
            'items.*.quantity' => 'Jumlah',
            'items.*.cost_price' => 'Harga',
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
            $pembelian = Pembelian::updateOrCreate([
                'id_pembelian' => $request->id_pembelian,
            ],[
                'supplier_id' => $request->supplier_id,
                'tanggal_pembelian' => date('Y-m-d',strtotime($request->tgl_pembelian)).' '.date('H:i:s'),
                'no_faktur' => $request->no_faktur,
                'total_pembelian' => $request->total,
                'metode_pembayaran' => $request->metode_pembayaran??null,
                'status_pembelian' => isset($request->status_pembelian) ? $request->status_pembelian : 'proses',
                'catatan' => $request->catatan??null,
            ]);

            // Process multiple variants
            $existingPembelianDetailIds = PembelianDetail::where('pembelian_id', $pembelian->id_pembelian)
                ->pluck('id_pembelian_detail')
                ->toArray();
            $pembelianDetailIdsInRequest = [];

            foreach ($request->items as $item) {

                $pembelian_detail = (isset($item['id_pembelian_detail']) && $item['id_pembelian_detail']) ? PembelianDetail::find($item['id_pembelian_detail']) : new PembelianDetail;
                $pembelian_detail->pembelian_id = $pembelian->id_pembelian;
                $pembelian_detail->produk_varian_id = $item['produk_varian_id'];
                $pembelian_detail->qty = $item['quantity'];
                $pembelian_detail->harga_satuan = $item['cost_price'];
                $pembelian_detail->sub_total = $item['subtotal'];
                $pembelian_detail->save();

                $pembelianDetailIdsInRequest[] = $pembelian_detail->id_pembelian_detail;

                // Update product stock quantity
                if((isset($item['id_pembelian_detail']) && $item['id_pembelian_detail']) && (isset($request->status_pembelian) && $request->status_pembelian == 'berhasil')){
                    $produk_varian = ProdukVarian::find($item['produk_varian_id']);
                    $produk_varian->stok_sekarang = ($produk_varian->stok_sekarang ?? 0) + $item['quantity'];
                    $produk_varian->harga_beli = $item['cost_price'];
                    $produk_varian->save();
                }
            }

            $pembelianDetailToDelete = array_diff($existingPembelianDetailIds, $pembelianDetailIdsInRequest);
            if (!empty($pembelianDetailToDelete)) {
                PembelianDetail::whereIn('id_pembelian_detail', $pembelianDetailToDelete)->delete();
            }

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
            $pembelian = Pembelian::findOrFail($id);
            PembelianDetail::where('pembelian_id',$pembelian->id_pembelian)->delete();
            $pembelian->delete();


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

        $data['data'] = Pembelian::with(['supplier','pembelian_detail.produk_varian.produk'])->find($request->id);

        return view('pages.pembelian.print',$data);
    }
}
