<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use App\Models\KategoriProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Kategori Produk',
            'Deskripsi',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $kategori_produk =  KategoriProduk::get();

        $data['config'] = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [null, ['orderable' => false], ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($kategori_produk as $item){
            if (auth()->user()->can('kategori_produk-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_kategori_produk.',`'.$item->kategori_produk.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('kategori_produk-edit')) {
                $btnDetails = '<a href="'.route('kategori_produk.form', ['id' => $item->id_kategori_produk]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $data['config']['data'][] = [
                $no++,
                $item->kategori_produk,
                $item->deskripsi,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.kategori_produk.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? KategoriProduk::find($request->id) : [];
        return view('pages.kategori_produk.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'kategori_produk' => 'required',
            // 'deskripsi' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'kategori_produk' => 'Kategori Produk',
            'deskripsi' => 'Deskripsi',
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
            KategoriProduk::updateOrCreate([
                'id_kategori_produk' => $request->id_kategori_produk,
            ],[
                'kategori_produk' => $request->kategori_produk,
                'deskripsi' => $request->deskripsi,
                'is_aktif' => isset($request->is_aktif) ? $request->is_aktif : true,
            ]);

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
            KategoriProduk::findOrFail($id)->delete();

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
