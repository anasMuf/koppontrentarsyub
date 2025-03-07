<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Helpers\LogPretty;
use App\Models\KategoriProduk;
use App\Models\ProdukVarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            ['label' => 'Kategori', 'width' => 10],
            'Nama Produk',
            ['label' => 'Varian Produk', 'width' => 20],
            'Stok',
            'Harga Jual',
            // ['label' => 'Harga Jual', 'classes' => 'text-end'],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $produk =  Produk::with(['kategori_produk','produk_varian'])->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, ['orderable' => false], ['orderable' => false], ['orderable' => false], ['orderable' => false], ['orderable' => false, 'classes' => 'text-right'], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($produk as $item){
            if (auth()->user()->can('produk-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_produk.',`'.$item->produk.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('produk-edit')) {
                $btnDetails = '<a href="'.route('produk.form', ['id' => $item->id_produk]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $variant = '';
            $name = [];
            foreach($item->produk_varian as $produk_varian){
                $name[] = $produk_varian->nama_produk_varian;
            }
            $variant = implode(', ',$name);

            $stock = 0;
            foreach($item->produk_varian as $key => $produk_varian){
                $stock += $produk_varian->stok_sekarang;
            }

            $price = '';
            $arrPrice = [];
            foreach($item->produk_varian as $key => $produk_varian){
                if($key == 0 || $key+1 == count($item->produk_varian)){
                    $arrPrice[] = 'Rp '.number_format($produk_varian->harga_jual,0,',','.');
                }
            }
            $arrPrice = array_unique($arrPrice);
            sort($arrPrice);
            $price = implode(' - ',$arrPrice);

            $data['config']['data'][] = [
                $no++,
                $item->kategori_produk ? $item->kategori_produk->kategori_produk : '-',
                $item->nama_produk,
                $variant,
                number_format($stock,0,',','.'),
                $price,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.produk.index',$data);
    }

    public function search(Request $request){
        if(isset($request->id)){
            $produk = Produk::with(['produk_varian'])
            ->find($request->id);

            return response()->json([
                'success' => true,
                'variants' => $produk->produk_varian
            ]);
        }
        $term = $request->term;

        $produk = Produk::with(['kategori_produk', 'produk_varian'])
            ->when($term,function($q) use ($term){
                $q->where('nama_produk', 'LIKE', "%{$term}%");
            })
            // ->orWhere('kode_produk', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();

        if(isset($request->from) && $request->from === 'pos'){
            $content = view('pages.penjualan.produk_card',compact('produk'))->render();
            return response()->json([
                'success' => true,
                'content' => $content
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Produk::with('produk_varian')->find($request->id) : [];
        $data['kategori_produk'] = array_map(function($item){
            return ['text' => $item['kategori_produk'], 'value' => $item['id_kategori_produk']];
        },KategoriProduk::get()->toArray());
        return view('pages.produk.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_produk' => 'required',
            'deskripsi' => 'nullable|string',
            'kategori_produk_id' => 'sometimes',
            'has_variants' => 'sometimes',
            'single_stock' => 'nullable|integer|min:0',
            'single_price' => 'nullable|numeric|min:0',
            'variants' => 'required_if:has_variants,on|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_produk' => 'Nama Produk',
            'deskripsi' => 'deskripsi Produk',
            'kategori_produk_id' => 'Kategori Produk',
            'has_variants' => 'Pilihan Memiliki Varian',
            'single_stock' => 'Stok',
            'single_price' => 'Price',
            'variants' => 'Varian',
            'variants.*.name' => 'Nama Varian',
            'variants.*.stock' => 'Stok Varian',
            'variants.*.price' => 'Harga Varian',
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
            $produk = ($request->id_produk) ? Produk::find($request->id_produk) : new Produk;
            $produk->nama_produk = $request->nama_produk;
            $produk->deskripsi = $request->deskripsi;
            $produk->kategori_produk_id = $request->kategori_produk_id;
            $produk->is_varian = $request->has('has_variants') ? 1 : 0;
            $produk->save();

            // Handle variants or single product data
            if ($request->has('has_variants')) {
                // Process multiple variants
                $existingVariantIds = ProdukVarian::where('produk_id', $produk->id_produk)
                    ->pluck('id_produk_varian')
                    ->toArray();
                $variantIdsInRequest = [];

                foreach ($request->variants as $variantData) {
                    $variant = (isset($variantData['id_produk_varian'])) ? ProdukVarian::find($variantData['id_produk_varian']) : new ProdukVarian;
                    $variant->produk_id = $produk->id_produk;
                    $variant->nama_produk_varian = $variantData['name'];
                    $variant->stok_sekarang = $variantData['stock']??0;
                    $variant->harga_jual = $variantData['price']??0;
                    $variant->save();

                    $variantIdsInRequest[] = $variant->id_produk_varian;
                }

                $variantsToDelete = array_diff($existingVariantIds, $variantIdsInRequest);
                if (!empty($variantsToDelete)) {
                    ProdukVarian::whereIn('id_produk_varian', $variantsToDelete)->delete();
                }
            } else {
                // Process single product as a default variant
                $variant = ($request->id_produk_varian) ? ProdukVarian::find($request->id_produk_varian) : new ProdukVarian;;
                $variant->produk_id = $produk->id_produk;
                $variant->nama_produk_varian = null; // null variant name for non-variant products
                $variant->stok_sekarang = $request->single_stock??0;
                $variant->harga_jual = $request->single_price??0;
                $variant->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, kesalahan pada sistem',
            ]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            $produk = Produk::findOrFail($id);
            ProdukVarian::where('produk_id',$produk->id_produk)->delete();
            $produk->delete();

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
}
