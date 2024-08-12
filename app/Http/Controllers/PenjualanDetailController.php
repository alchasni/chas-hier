<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('name')->get();
        $guest = Guest::orderBy('nama')->get();
        $diskon = 0;

        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Transaction::find($id_penjualan);
            $guestSelected = $penjualan->guest ?? new Guest();

            return view('penjualan_detail.index', compact('product', 'guest', 'diskon', 'id_penjualan', 'penjualan', 'guestSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = TransactionDetail::with('product')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['code'] = '<span class="label label-success">'. $item->product['code'] .'</span';
            $row['name'] = $item->product['name'];
            $row['harga_jual']  = 'Rp. '. money_number_format($item->harga_jual);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'">';
            $row['diskon']      = $item->diskon . '%';
            $row['subtotal']    = 'Rp. '. money_number_format($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->harga_jual * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'code' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'name' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'code', 'jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $product = Product::where('product_id', $request->product_id)->first();
        if (! $product) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new TransactionDetail();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->product_id = $product->product_id;
        $detail->harga_jual = $product->harga_jual;
        $detail->jumlah = 1;
        $detail->diskon = 0;
        $detail->subtotal = $product->harga_jual;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = TransactionDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = TransactionDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0)
    {
        $final_price   = $total - ($diskon / 100 * $total);
        $kembali = ($diterima != 0) ? $diterima - $final_price : 0;
        $data    = [
            'totalrp' => money_number_format($total),
            'final_price' => $final_price,
            'final_pricerp' => money_number_format($final_price),
            'terbilang' => ucwords(money_written_format($final_price). ' Rupiah'),
            'kembalirp' => money_number_format($kembali),
            'kembali_terbilang' => ucwords(money_written_format($kembali). ' Rupiah'),
        ];

        return response()->json($data);
    }
}
