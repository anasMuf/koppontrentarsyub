<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use App\Models\PendaftaranAnggota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            ['label' => 'No Anggota', 'width' => 17],
            'Nama Lengkap',
            ['label' => 'Status Anggota', 'width' => 17],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $anggota =  Anggota::get();

        $data['config'] = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [null, null, ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $status = '';
        $no = 1;

        foreach($anggota as $item){
            if (auth()->user()->can('anggota-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_anggota.',`'.$item->nama_lengkap.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            if (auth()->user()->can('anggota-edit')) {
                $btnDetails = '<a href="'.route('anggota.form', ['id' => $item->id_anggota]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }
            if($item->status_anggota === 'aktif'){
                $status = '<span class="badge badge-success">Aktif</span>';
            }else{
                $status = '<span class="badge badge-danger">Non-Aktif</span>';
            }
            $data['config']['data'][] = [
                $no++,
                $item->no_anggota,
                $item->nama_lengkap,
                $status,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.anggota.index',$data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Anggota::find($request->id) : [];
        return view('pages.anggota.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'no_anggota' => 'required',
            'tgl_bergabung' => 'required|date',
            'nama_lengkap' => 'required',
            'no_ktp' => 'required|integer',
            'no_telepon' => 'required|integer',
            'pekerjaan' => 'required',
            'alamat' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'integer' => ':attribute harus berupa angka',
            'date' => ':attribute harus berupa format tanggal',
        ];
        $attributes = [
            'no_anggota' => 'No Anggota',
            'tgl_bergabung' => 'Tanggal Bergabung',
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
            $anggota = Anggota::find($request->id_anggota);
            $anggota->no_anggota = $request->no_anggota;
            $anggota->tgl_bergabung = $request->tgl_bergabung;
            $anggota->nama_lengkap = $request->nama_lengkap;
            $anggota->no_ktp = $request->no_ktp;
            $anggota->no_telepon = $request->no_telepon;
            $anggota->pekerjaan = $request->pekerjaan;
            $anggota->alamat = $request->alamat;
            // $anggota->status_anggota = $request->status_anggota;
            // if($request->status_anggota === 'nonaktif'){
            //     $anggota->tgl_keluar = date('Y-m-d');
            // }
            $anggota->save();

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

    public function nonaktif(Request $request){
        $data['data'] = ($request->id) ? Anggota::find($request->id) : [];
        return view('pages.anggota.nonaktif',$data);
    }

    public function nonaktifStore(Request $request){
        return 'dalam perbaikan';
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            $anggota = Anggota::findOrFail($id);
            $user = User::findOrFail($anggota->user_id)->delete();
            $pendaftaranAnggota = PendaftaranAnggota::findOrFail($anggota->pendaftaran_anggota_id)->delete();
            $anggota->delete();

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
