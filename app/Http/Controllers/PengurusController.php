<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengurus;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PengurusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Lengkap',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pengurus = [];
        if (auth()->user()->roles->pluck('name')[0] == 'admin') {
            $pengurus =  Pengurus::get();
        }elseif (auth()->user()->roles->pluck('name')[0] == 'pengurus') {
            $pengurus =  Pengurus::where('user_id',auth()->user()->id)->get();
        }

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($pengurus as $item){
            if (auth()->user()->can('pengurus-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_pengurus.',`'.$item->nama_lengkap.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('pengurus-edit')) {
                $btnDetails = '<a href="'.route('pengurus.form', ['id' => $item->id_pengurus]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }
            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pengurus.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Pengurus::with('user')->find($request->id) : [];
        return view('pages.pengurus.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_lengkap' => 'required',
            'password' => 'required',
        ];
        if($request->id_pengurus){
            $rules[] = [
                'username' => 'required',
                'email' => 'required|email',
            ];
        }else{
            $rules[] = [
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
            ];
        }
        $messages = [
            'required' => ':attribute harus diisi',
            'unique' => ':attribute sudah digunakan',
            'email' => ':attribute harus berupa email',
        ];
        $attributes = [
            'nama_lengkap' => 'Nama Lengkap',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        ];
        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::updateOrCreate([
                'id' => $request->user_id
            ],[
                'name' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            if(!$request->user_id){
                $user->assignRole('pengurus');
            }

            Pengurus::updateOrCreate([
                'id_pengurus' => $request->id_pengurus
            ],[
                'nama_lengkap' => $request->nama_lengkap,
                'user_id' => $user->id
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
            $pengurus = Pengurus::findOrFail($id);
            User::findOrFail($pengurus->user_id)->delete();
            $pengurus->delete();

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
