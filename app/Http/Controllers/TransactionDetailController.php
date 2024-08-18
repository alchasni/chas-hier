<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionDetailController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        $guests = Guest::orderBy('name')->get();

        if ($transaction_id = session('transaction_id')) {
            $transaction = Transaction::find($transaction_id);
            $guestSelected = $transaction->guest ?? new Guest();

            return view('transaction_detail.index', compact('products', 'guests', 'transaction_id', 'transaction', 'guestSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaction.new');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = TransactionDetail::with('product')
            ->where('transaction_id', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['code'] = '<span class="label label-success">'. $item->product['code'] .'</span';
            $row['name'] = $item->product['name'];
            $row['sell_price']  = 'Rp. '. money_number_format($item->sell_price);
            $row['quantity']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->transaction_detail_id .'" value="'. $item->quantity .'">';
            $row['price']    = 'Rp. '. money_number_format($item->price);
            $row['action']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaction_detail.destroy', $item) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->sell_price * $item->quantity;
            $total_item += $item->quantity;
        }
        $data[] = [
            'code' => '
                <div class="total_price hide">'. $total .'</div>
                <div class="total_item_quantity hide">'. $total_item .'</div>',
            'name' => '',
            'sell_price'  => '',
            'quantity'      => '',
            'price'    => '',
            'action'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['action', 'code', 'quantity'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $product = Product::where('product_id', $request->product_id)->first();
        if (! $product) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new TransactionDetail();
        $detail->transaction_id = $request->transaction_id;
        $detail->product_id = $product->product_id;
        $detail->sell_price = $product->sell_price;
        $detail->quantity = 1;
        $detail->price = $product->sell_price;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = TransactionDetail::find($id);
        $detail->quantity = $request->quantity;
        $detail->price = $detail->sell_price * $request->quantity;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = TransactionDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($total = 0, $diterima = 0)
    {
        $kembali = ($diterima != 0) ? $diterima - $total : 0;
        $data    = [
            'totalrp' => money_number_format($total),
            'final_price' => $total,
            'final_pricerp' => money_number_format($total),
            'terbilang' => ucwords(money_written_format($total). ' Rupiah'),
            'kembalirp' => money_number_format($kembali),
            'kembali_terbilang' => ucwords(money_written_format($kembali). ' Rupiah'),
        ];

        return response()->json($data);
    }
}
