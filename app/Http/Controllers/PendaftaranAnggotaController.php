<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use App\Models\PendaftaranAnggota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PendaftaranAnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            ['label' => 'No Formulir', 'width' => 15],
            'Nama Lengkap',
            ['label' => 'Status Pendaftaran', 'width' => 20],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pendaftaran_anggota =  PendaftaranAnggota::get();

        $data['config'] = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [null, null, ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $status = '';
        $no = 1;

        foreach($pendaftaran_anggota as $item){
            if (auth()->user()->can('pendaftaran_anggota-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_pendaftaran_anggota.',`'.$item->nama_lengkap.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('pendaftaran_anggota-edit')) {
                $btnDetails = '<a href="'.route('pendaftaran_anggota.form', ['id' => $item->id_pendaftaran_anggota]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }
            if($item->status_pendaftaran === 'terima'){
                $status = '<span class="badge badge-success">Diterima</span>';
            }elseif($item->status_pendaftaran === 'tolak'){
                $status = '<span class="badge badge-danger">Ditolak</span>';
            }else{
                $status = '<span class="badge badge-warning">Proses</span>';
            }
            $data['config']['data'][] = [
                $no++,
                $item->no_formulir,
                $item->nama_lengkap,
                $status,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pendaftaran-anggota.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? PendaftaranAnggota::find($request->id) : [];
        $data['no_formulir'] = PendaftaranAnggota::no_formulir();
        return view('pages.pendaftaran-anggota.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'no_formulir' => 'required',
            'tgl_pendaftaran' => 'required|date',
            'nama_lengkap' => 'required',
            'no_ktp' => 'required|integer',
            'no_telepon' => 'required|numeric',
            'pekerjaan' => 'required',
            'alamat' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa angka',
            'date' => ':attribute harus berupa format tanggal',
        ];
        $attributes = [
            'no_formulir' => 'No Formulir',
            'tgl_pendaftaran' => 'Tanggal Pendaftaran',
            'nama_lengkap' => 'Nama Lengkap',
            'no_ktp' => 'No. KTP',
            'no_telepon' => 'No. Telepon',
            'pekerjaan' => 'Pekerjaan',
            'alamat' => 'Alamat',
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
            if($request->id_pendaftaran_anggota){
                $pendaftaranAnggota = PendaftaranAnggota::find($request->id_pendaftaran_anggota);
            }else{
                $pendaftaranAnggota = new PendaftaranAnggota;
                $pendaftaranAnggota->status_pendaftaran = 'proses';
                $pendaftaranAnggota->user_id = Auth::user()->id;
            }

            $pendaftaranAnggota->no_formulir = $request->no_formulir;
            $pendaftaranAnggota->tgl_pendaftaran = $request->tgl_pendaftaran;
            $pendaftaranAnggota->nama_lengkap = $request->nama_lengkap;
            $pendaftaranAnggota->no_ktp = $request->no_ktp;
            $pendaftaranAnggota->no_telepon = $request->no_telepon;
            $pendaftaranAnggota->pekerjaan = $request->pekerjaan;
            $pendaftaranAnggota->alamat = $request->alamat;
            $pendaftaranAnggota->save();

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

    public function updateStatus(Request $request){
        $rules = [
            'id_pendaftaran_anggota' => 'required',
            'status_pendaftaran' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'id_pendaftaran_anggota' => 'Id Pendaftaran Anggota',
            'status_pendaftaran' => 'Status Pendaftaran',
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
            $pendaftaranAnggota = PendaftaranAnggota::find($request->id_pendaftaran_anggota);
            $pendaftaranAnggota->status_pendaftaran = $request->status_pendaftaran;
            if($request->status_pendaftaran === 'terima'){
                $pendaftaranAnggota->tanggal_persetujuan = date('Y-m-d H:i:s');
            }else{
                $pendaftaranAnggota->tanggal_penolakan = date('Y-m-d H:i:s');
            }
            $pendaftaranAnggota->user_id = Auth::user()->id;
            $pendaftaranAnggota->save();

            if($request->status_pendaftaran === 'terima'){
                $user = User::create([
                    'name' => $pendaftaranAnggota->nama_lengkap,
                    'username' => $pendaftaranAnggota->no_ktp,
                    'email' => $pendaftaranAnggota->no_ktp.'@test.id',
                    'password' => Hash::make($pendaftaranAnggota->no_ktp),
                ]);
                if(!$request->user_id){
                    $user->assignRole('anggota');
                }

                Anggota::create([
                    'pendaftaran_anggota_id' => $pendaftaranAnggota->id_pendaftaran_anggota,
                    'no_anggota' => Anggota::no_anggota(),
                    'nama_lengkap' => $pendaftaranAnggota->nama_lengkap,
                    'no_ktp' => $pendaftaranAnggota->no_ktp,
                    'pekerjaan' => $pendaftaranAnggota->pekerjaan,
                    'alamat' => $pendaftaranAnggota->alamat,
                    'no_telepon' => $pendaftaranAnggota->no_telepon,
                    'tgl_bergabung' => date('Y-m-d'),
                    'user_id' => $user->id,
                ]);
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
            $pendaftaranAnggota = PendaftaranAnggota::findOrFail($id);
            $pendaftaranAnggota->delete();

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
