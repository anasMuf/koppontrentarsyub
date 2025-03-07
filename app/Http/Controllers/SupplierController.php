<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Supplier',
            'Alamat',
            'No. Telepon',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $supplier =  Supplier::get();

        $data['config'] = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [null, ['orderable' => false], ['orderable' => false], ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($supplier as $item){
            if (auth()->user()->can('supplier-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_supplier.',`'.$item->supplier.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('supplier-edit')) {
                $btnDetails = '<a href="'.route('supplier.form', ['id' => $item->id_supplier]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $data['config']['data'][] = [
                $no++,
                $item->nama_supplier,
                $item->alamat,
                $item->no_telepon,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.supplier.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Supplier::find($request->id) : [];
        return view('pages.supplier.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'no_telepon' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_supplier' => 'Nama Supplier',
            'alamat' => 'Alamat',
            'no_telepon' => 'No Telepon',
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
            Supplier::updateOrCreate([
                'id_supplier' => $request->id_supplier,
            ],[
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'email' => $request->email,
                'npwp' => $request->npwp,
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
            Supplier::findOrFail($id)->delete();

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
