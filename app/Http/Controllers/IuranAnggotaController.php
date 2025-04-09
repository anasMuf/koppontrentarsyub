<?php

namespace App\Http\Controllers;

use App\Helpers\Bulan;
use App\Models\Anggota;
use App\Helpers\LogPretty;
use App\Models\JenisIuran;
use App\Models\Pengaturan;
use App\Models\IuranAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IuranAnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Anggota',
            ['label' => 'Iuran Wajib', 'width' => 17],
            ['label' => 'Periode Iuran Pokok', 'width' => 17],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $anggota =  Anggota::with('iuran_anggota.jenis_iuran')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false], ['orderable' => false], ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($anggota as $item){
            $sudahWajib = false;
            $iuran_wajib = '';
            $periode_iuran = '';
            if (auth()->user()->can('iuran_anggota-edit')) {
                $btnDetails = '<a href="'.route('iuran_anggota.detail', ['anggota' => $item->id_anggota]).'" class="btn btn-info btn-xs mx-1" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';
            }

            $bulanAnggota = null;
            $tahun = date('Y',strtotime($item->tgl_bergabung));
            if($tahun == date('Y')){
                $bulanAnggota = Bulan::filterByJoinDate(date('m',strtotime($item->tgl_bergabung)));
            }else{
                $bulanAnggota = Bulan::list();
            }


            foreach($bulanAnggota as $bulan){
                $sudahDibayar = false;

                if($item->iuran_anggota){
                    foreach($item->iuran_anggota as $iuran_anggota){

                        if($iuran_anggota && $iuran_anggota->jenis_iuran && $iuran_anggota->jenis_iuran->jenis_iuran === 'wajib'){
                            $sudahWajib = true;
                        }

                        if($iuran_anggota && date('Y-m',strtotime($iuran_anggota->periode_iuran)) === date('Y').'-'.$bulan['bulan_angka']){
                            $sudahDibayar = true;
                            break;
                        }
                    }
                }

                if($sudahDibayar) {
                    $periode_iuran .= '<span class="badge badge-success">'.$bulan['bulan_nama'].'</span>';
                } else {
                    $periode_iuran .= '<span class="badge badge-light">'.$bulan['bulan_nama'].'</span>';
                }
            }

            if($sudahWajib){
                $iuran_wajib = '<span class="badge badge-success">Sudah Bayar</span>';
            } else {
                $iuran_wajib = '<span class="badge badge-light">Belum Bayar</span>';
            }

            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                $iuran_wajib,
                $periode_iuran,
                '<nobr>'.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.iuran_anggota.index',$data);
    }

    public function detail(Anggota $anggota){
        $data['anggota'] = $anggota;

        $iuran_anggota =  $anggota->load('iuran_anggota.jenis_iuran');

        $jenis_iuran = JenisIuran::where('jenis_iuran','wajib')->first();

        // =============================

        $data['heads1'] = [
            'Jenis Iuran',
            ['label' => 'Nominal', 'width' => 17],
            ['label' => 'Status', 'width' => 17],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $data['config1'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [['orderable' => false], ['orderable' => false], ['orderable' => false], ['orderable' => false]],
        ];
        $data['config1']["searching"] = false;
        $data['config1']["paging"] = false;
        $data['config1']["info"] = false;
        $btnDetails1 = '';
        $btnPaymentDues1 = '';
        $sudahWajib = false;
        $status1 = '';

        foreach($iuran_anggota->iuran_anggota as $item){
            if($item->jenis_iuran->jenis_iuran === 'wajib'){
                $sudahWajib = true;
                break;
            }
        }

        if($sudahWajib){
            $status1 = '<span class="badge badge-success">Sudah Bayar</span>';

            if (auth()->user()->can('iuran_anggota-edit')) {
                $btnDetails1 = '<a href="'.route('iuran_anggota.payment', ['anggota' => $anggota->id_anggota,'periode_iuran'=>'wajib']).'" class="btn btn-info btn-xs mx-1" title="Details">
                    <i class="fa fa-lg fa-fw fa-eye"></i>
                </a>';
            }
        } else {
            $status1 = '<span class="badge badge-light">Belum Bayar</span>';

            if (auth()->user()->can('iuran_anggota-edit')) {
                $btnPaymentDues1 = '<a href="'.route('iuran_anggota.payment', ['anggota' => $anggota->id_anggota,'periode_iuran'=>'wajib']).'" class="btn btn-warning btn-xs mx-1" title="Payment">
                                <i class="fa fa-lg fa-fw fa-coins"></i>
                            </a>';
            }
        }

        $data['config1']['data'][] = [
            strtoupper($jenis_iuran->jenis_iuran),
            number_format($jenis_iuran->nominal,0,',','.'),
            $status1,
            '<nobr>'.$btnPaymentDues1.$btnDetails1.'</nobr>'
        ];

        // =============================

        $data['heads2'] = [
            ['label' => 'No', 'width' => 4],
            'Bulan',
            ['label' => 'Status', 'width' => 17],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $data['config2'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false], ['orderable' => false]],
        ];
        $data['config2']["lengthMenu"] = [12];

        $btnDelete2 = '';
        $btnDetails2 = '';
        $btnPaymentDues2 = '';
        $status2 = '';
        $no2 = 1;


        $bulanAnggota = null;
        $tahun = date('Y',strtotime($anggota->tgl_bergabung));
        if($tahun == date('Y')){
            $bulanAnggota = Bulan::filterByJoinDate(date('m',strtotime($anggota->tgl_bergabung)));
        }else{
            $bulanAnggota = Bulan::list();
        }

        foreach($bulanAnggota as $bulan){
            $sudahDibayar = false;
            $btnDetails2 = '';
            $btnPaymentDues2 = '';

            foreach($iuran_anggota->iuran_anggota as $item){
                if(date('Y-m',strtotime($item->periode_iuran)) === date('Y').'-'.$bulan['bulan_angka']){
                    $sudahDibayar = true;
                    break;
                }
            }

            if($sudahDibayar) {
                $status2 = '<span class="badge badge-success">Dibayar</span>';

                if (auth()->user()->can('iuran_anggota-edit')) {
                    $btnDetails2 = '<a href="'.route('iuran_anggota.payment', ['anggota' => $anggota->id_anggota,'periode_iuran'=>$tahun.'-'.$bulan['bulan_angka']]).'" class="btn btn-info btn-xs mx-1" title="Details">
                        <i class="fa fa-lg fa-fw fa-eye"></i>
                    </a>';
                }
            } else {
                $status2 = '<span class="badge badge-light">Belum Dibayar</span>';

                if (auth()->user()->can('iuran_anggota-edit')) {
                    $btnPaymentDues2 = '<a href="'.route('iuran_anggota.payment', ['anggota' => $anggota->id_anggota,'periode_iuran'=>$tahun.'-'.$bulan['bulan_angka']]).'" class="btn btn-warning btn-xs mx-1" title="Payment">
                                    <i class="fa fa-lg fa-fw fa-coins"></i>
                                </a>';
                }
            }

            $data['config2']['data'][] = [
                $no2++,
                $bulan['bulan_nama'],
                $status2,
                '<nobr>'.$btnPaymentDues2.$btnDetails2.'</nobr>'
            ];
        }

        return view('pages.iuran_anggota.detail',$data);
    }

    public function payment(Anggota $anggota, $periode_iuran){

        if($periode_iuran !== 'wajib' && strtotime($periode_iuran)){
            $bulan = '';
            $bulan_angka = date('m',strtotime($periode_iuran));
            $tahun = date('Y',strtotime($periode_iuran));
            foreach(Bulan::filtered($bulan_angka) as $bulan){
                if($bulan['bulan_angka'] === $bulan_angka){
                    $bulan = $bulan['bulan_nama'];
                    break;
                }
            }
            $data['bulan'] = $bulan;
            $data['periode_iuran'] = $periode_iuran;
            $data['data'] = IuranAnggota::whereMonth('periode_iuran',$bulan_angka)->whereYear('periode_iuran',$tahun)->first()
            ?? [];
            $data['jenis_iuran'] = JenisIuran::where('jenis_iuran','pokok')->first();
            $data['anggota'] = $anggota;

            return view('pages.iuran_anggota.payment',$data);
        }elseif($periode_iuran === 'wajib'){
            $data['bulan'] = '';
            $data['periode_iuran'] = $periode_iuran;
            $data['data'] = IuranAnggota::where('jenis_iuran_id',1)->first() ?? [];
            $data['jenis_iuran'] = JenisIuran::where('jenis_iuran','wajib')->first();
            $data['anggota'] = $anggota;
            return view('pages.iuran_anggota.payment',$data);
        }else{
            return 'error';
        }
    }

    public function store(Request $request){
        $rules = [
            'anggota_id' => 'required',
            'jenis_iuran_id' => 'required',
            'nominal' => 'required|integer',
            'nominalFormated' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi/sesuai',
            'integer' => ':attribute harus berupa angka',
            'date_format' => ':attribute tidak sesuai format bulan-tahun',
        ];
        $attributes = [
            'anggota_id' => 'Anggota',
            'jenis_iuran_id' => 'Jenis Iuran',
            'periode_iuran' => 'Periode Iuran',
            'nominalFormated' => 'Nominal',
            'nominal' => 'Nominal',
        ];
        if($request->jenis_iuran === 'pokok'){
            $rules[]['periode_iuran'] = 'required|date_format:Y-m';
            $messages[]['date_format'] = ':attribute tidak sesuai format bulan-tahun';
            $attributes[]['periode_iuran'] = 'Periode Iuran';
        }
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
            if(IuranAnggota::where([
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'anggota_id' => $request->anggota_id,
                'periode_iuran' => $request->periode_iuran.'-01',
                'status_pembayaran' => 'sudah_bayar',
            ])->first()){
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data pembayaran iuran anggota, iuran sudah dibayar',
                ]);
            }

            IuranAnggota::create([
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'anggota_id' => $request->anggota_id,
                'nominal' => $request->jenis_iuran_id != 3 ? $request->nominal : 0,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'periode_iuran' => $request->jenis_iuran_id == 2 ? $request->periode_iuran.'-01' : date('Y-m-d'),
                'status_pembayaran' => 'sudah_bayar',
                'metode_pembayaran' => 'cash',
                'user_id' => Auth::user()->id,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data pembayaran iuran anggota',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data pembayaran iuran anggota, kesalahan pada sistem',
            ]);
        }
    }

    public function print(Anggota $anggota, $periode_iuran){

        $data['nama'] = Pengaturan::where('setting','nama')->first()->value ?? '';
        $data['logo'] = Pengaturan::where('setting','logo')->first()->value ?? '';
        $data['alamat'] = Pengaturan::where('setting','alamat')->first()->value ?? '';
        $data['telepon'] = Pengaturan::where('setting','telepon')->first()->value ?? '';
        $data['email'] = Pengaturan::where('setting','email')->first()->value ?? '';

        if($periode_iuran !== 'wajib' && strtotime($periode_iuran)){
            $bulan = '';
            $bulan_angka = date('m',strtotime($periode_iuran));
            $tahun = date('Y',strtotime($periode_iuran));
            foreach(Bulan::filtered($bulan_angka) as $bulan){
                if($bulan['bulan_angka'] === $bulan_angka){
                    $bulan = $bulan['bulan_nama'];
                    break;
                }
            }
            $data['bulan'] = $bulan;
            $data['periode_iuran'] = $periode_iuran;
            $data['data'] = IuranAnggota::whereMonth('periode_iuran',$bulan_angka)->whereYear('periode_iuran',$tahun)->first();
            $data['jenis_iuran'] = JenisIuran::where('jenis_iuran','pokok')->first();
            $data['anggota'] = $anggota;

            return view('pages.iuran_anggota.pokok.print',$data);
        }elseif($periode_iuran === 'wajib'){
            $data['bulan'] = '';
            $data['periode_iuran'] = $periode_iuran;
            $data['data'] = IuranAnggota::where('jenis_iuran_id',1)->first() ?? [];
            $data['jenis_iuran'] = JenisIuran::where('jenis_iuran','wajib')->first();
            $data['anggota'] = $anggota;
            return view('pages.iuran_anggota.wajib.print',$data);
        }else{
            return 'error';
        }
    }
}
