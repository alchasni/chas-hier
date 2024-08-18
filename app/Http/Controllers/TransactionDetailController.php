<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionDetailController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
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

    /**
     * @throws Exception
     */
    public function data($id)
    {
        try {
            $detail = TransactionDetail::with('product')
                ->where('transaction_id', $id)
                ->get();

            $data = array();
            $total = 0;
            $total_item = 0;

            foreach ($detail as $item) {
                $row = array();
                $row['product_id'] = '<span class="label label-success trx_detail_product_id">'. $item->product['code'] .'</span';
                $row['code'] = '<span class="label label-success">'. $item->product['code'] .'</span';
                $row['name'] = $item->product['name'];
                $row['sell_price']  = 'Rp. '. money_number_format($item->sell_price);
                $row['quantity']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->transaction_detail_id .'" data-product_id="'. $item->product_id .'" value="'. $item->quantity .'">';
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
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load data'], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'transaction_id' => 'required|exists:transaction,transaction_id',
        ]);

        $product = Product::where('product_id', $validatedData['product_id'])->first();
        if (!$product) {
            return response()->json(['error' => 'Could not save the data'], 500);
        }

        $requestedQuantity = 1;
        if ($product->stock < $requestedQuantity) {
            return response()->json(['error' => 'Insufficient stock for product: ' . $product->name], 400);
        }

        $detail = new TransactionDetail();
        $data = [
            'transaction_id' => $validatedData['transaction_id'],
            'product_id' => $product->product_id,
            'sell_price' => $product->sell_price,
            'quantity' => $requestedQuantity,
            'price' => $product->sell_price
        ];

        return $this->saveModel($data, $detail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $detail = TransactionDetail::find($id);
        if (!$detail) {
            return response()->json(['error' => 'Transaction detail not found'], 404);
        }

        $product = Product::find($detail->product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        if ($product->stock < $validatedData['quantity']) {
            return response()->json(['error' => 'Insufficient stock for product: ' . $product->name], 400);
        }

        $data = [
            'quantity' => $validatedData['quantity'],
            'price' => $detail->sell_price * $validatedData['quantity'],
        ];

        return $this->saveModel($data, $detail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $detail = TransactionDetail::find($id);
        if ($detail) {
            $detail->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }

        return response()->json(['error' => 'Transaction Detail not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $total
     * @param int $money_received
     * @return JsonResponse
     */
    public function loadForm(int $total = 0, int $money_received = 0): JsonResponse
    {
        $change = ($money_received != 0) ? $money_received - $total : 0;
        $data    = [
            'totalrp' => money_number_format($total),
            'final_price' => $total,
            'final_pricerp' => money_number_format($total),
            'in_word' => ucwords(money_written_format($total). ' Rupiah'),
            'changerp' => money_number_format($change),
            'change_in_word' => ucwords(money_written_format($change). ' Rupiah'),
        ];

        return response()->json($data);
    }
}
