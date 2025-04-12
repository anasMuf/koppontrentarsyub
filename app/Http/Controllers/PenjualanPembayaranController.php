<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use NumberFormatter;
use App\Models\Penjualan;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PenjualanPembayaran;
use Illuminate\Support\Facades\Validator;

class PenjualanPembayaranController extends Controller
{
    public function index(Penjualan $penjualan){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tanggal Bayar',
            'Nominal Dibayar',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $penjualan_pembayaran =  PenjualanPembayaran::where('penjualan_id',$penjualan->id_penjualan)->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null,  null, ['orderable' => false]],
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($penjualan_pembayaran as $item){
            if (auth()->user()->can('penjualan-delete')) {
                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id_penjualan_pembayaran.',`'.$item->penjualan->no_struk.'`)">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';
            }
            // if (auth()->user()->can('penjualan-edit')) {
            //     $btnDetails = '<a href="'.route('pembayaran_penjualan.form', ['penjualan' => $penjualan->id_penjualan,'id'=>$item->id_penjualan_pembayaran]).'" class="btn btn-info btn-xs mx-1" title="Details">
            //                     <i class="fa fa-lg fa-fw fa-eye"></i>
            //                 </a>';
            // }

            $format = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $format->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

            $data['config']['data'][] = [
                $no++,
                Carbon::parse($item->created_at)->isoFormat('DD MMMM YYYY'),
                $format->formatCurrency($item->nominal, 'IDR'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        $data['penjualan'] = $penjualan->load('anggota');

        return view('pages.penjualan.pembayaran.index',$data);
    }

    public function form(Request $request, Penjualan $penjualan){
        $data['penjualan'] = $penjualan;
        $data['data'] = ($request->id) ? PenjualanPembayaran::with('penjualan')->find($request->id) : [];
        return view('pages.penjualan.pembayaran.form',$data);
    }

    public function store(Request $request, Penjualan $penjualan) {
        $rules = [
            'penjualan_id' => 'required',
            'amount_paid' => 'required|numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa nominal uang',
            'min' => ':attribute tidak sesuai minimal nominal',
        ];
        $attributes = [
            'penjualan_id' => 'Penjualan',
            'amount_paid' => 'Jumlah Bayar',
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
            PenjualanPembayaran::updateOrCreate([
                'id_penjualan_pembayaran' => $request->id_penjualan_pembayaran
            ],[
                'penjualan_id' => $request->penjualan_id,
                'nominal' => $request->amount_paid,
            ]);

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

    public function delete(Penjualan $penjualan, $id){
        DB::beginTransaction();
        try {
            PenjualanPembayaran::findOrFail($id)->delete();

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
