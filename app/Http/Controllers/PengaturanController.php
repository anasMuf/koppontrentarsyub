<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Pengaturan',
            'Keterangan',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pengaturan = Pengaturan::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, ['orderable' => false], ['orderable' => false], ['orderable' => false]],
        ];
        $data['config']["searching"] = false;
        $data['config']["paging"] = false;
        $data['config']["info"] = false;

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($pengaturan as $item){
            $value = $item->value;
            // if (auth()->user()->can('pengaturan-delete')) {
            //     $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_pengaturan.',`'.$item->nama_lengkap.'`)">
            //                     <i class="fa fa-lg fa-fw fa-trash"></i>
            //                 </button>';
            // }
            if (auth()->user()->can('pengaturan-edit')) {
                $btnDetails = '<a href="'.route('pengaturan.form', ['id' => $item->id_pengaturan]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            if($item->setting == 'logo' && $item->value){
                $value = '<img src="'.asset('dist/images/logos/'.$item->value).'" width="50">';
            }

            $data['config']['data'][] = [
                $no++,
                $item->setting,
                $value,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pengaturan.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Pengaturan::find($request->id) : [];
        return view('pages.pengaturan.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'setting' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'setting' => 'Nama Pengaturan',
        ];
        if($request->setting == 'logo' && isset($request->value_file)){
            $rules[]['value_file'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            $messages[]['image'] = ':attribute harus format gambar';
            $messages[]['max'] = ':attribute melebihi 2 mb';
            $attributes[]['value_file'] = 'File Logo';
        }else{
            $rules[]['value'] = 'required';
            $attributes[]['value'] = 'Value Pengaturan';
        }
        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $value = $request->value;

            if($request->setting == 'logo' && isset($request->value_file) && $request->hasFile('value_file')){
                $value = time().$request->file('value_file')->getClientOriginalName();
                // $request->file('value_file')->storeAs('public/images/logos', $value);
                if(!move_uploaded_file($request->file('value_file')->getRealPath(),public_path('dist/images/logos/').$value)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal upload file, kesalahan pada sistem',
                    ]);
                }
            }

            Pengaturan::updateOrCreate([
                'id_pengaturan' => $request->id_pengaturan
            ],[
                'setting' => $request->setting,
                'value' => $value
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
            $pengaturan = Pengaturan::findOrFail($id);
            $pengaturan->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data',
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
