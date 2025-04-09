<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use App\Models\JenisIuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JenisIuranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Jenis Iuran',
            ['label' => 'Nominal', 'width' => 17],
            ['label' => 'Wajib Dibayar', 'width' => 17],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $jenis_iuran =  JenisIuran::get();

        $data['config'] = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [null, null, ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $wajib_dibayar = '';
        $no = 1;

        foreach($jenis_iuran as $item){
            if (auth()->user()->can('jenis_iuran-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_jenis_iuran.',`'.$item->jenis_iuran.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('jenis_iuran-edit')) {
                $btnDetails = '<a href="'.route('jenis_iuran.form', ['id' => $item->id_jenis_iuran]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }
            if($item->wajib_dibayar){
                $wajib_dibayar = '<span class="badge badge-warning">Wajib Bayar</span>';
            }else{
                $wajib_dibayar = '<span class="badge badge-light">Tidak Wajib Bayar</span>';
            }
            $data['config']['data'][] = [
                $no++,
                $item->jenis_iuran,
                $item->nominal > 0 ? number_format($item->nominal,0,',','.') : '',
                $wajib_dibayar,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.jenis_iuran.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? JenisIuran::find($request->id) : [];
        return view('pages.jenis_iuran.form',$data);
    }


    public function store(Request $request){
        $rules = [
            'jenis_iuran' => 'required',
            'keterangan' => 'required',
        ];
        if($request->jenis_iuran !== 'sukarela'){
            $rules[] = [
                'nominalFormated' => 'required',
                'nominal' => 'required|integer',
            ];
        }
        $messages = [
            'required' => ':attribute harus diisi',
            'integer' => ':attribute harus berupa angka',
        ];
        $attributes = [
            'jenis_iuran' => 'Jenis Iuran',
            'nominalFormated' => 'Nominal',
            'nominal' => 'Nominal',
            'keterangan' => 'Keterangan',
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
            JenisIuran::updateOrCreate([
                'id_jenis_iuran' => $request->id_jenis_iuran
            ],[
                'jenis_iuran' => $request->jenis_iuran,
                'nominal' => $request->jenis_iuran != 'sukarela' ? $request->nominal : 0,
                'keterangan' => $request->keterangan,
                'wajib_dibayar' => $request->jenis_iuran == 'wajib' || $request->jenis_iuran == 'pokok' ? true : false
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
            JenisIuran::findOrFail($id)->delete();

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
