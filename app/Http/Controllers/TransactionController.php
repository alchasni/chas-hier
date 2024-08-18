<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('transaction.index');
    }

    /**
     * @throws Exception
     */
    public function data()
    {
        $transactions = Transaction::with('guest')
            ->where('is_temp', false)
            ->orderBy('transaction_id', 'desc')
            ->get();

        return datatables()
            ->of($transactions)
            ->addIndexColumn()
            ->addColumn('total_item_quantity', function ($transaction) {
                return money_number_format($transaction->total_item_quantity);
            })
            ->addColumn('total_price', function ($transaction) {
                return 'Rp. '. money_number_format($transaction->total_price);
            })
            ->addColumn('final_price', function ($transaction) {
                return 'Rp. '. money_number_format($transaction->final_price);
            })
            ->addColumn('date', function ($transaction) {
                return to_date_string($transaction->created_at, false);
            })
            ->addColumn('member_code', function ($transaction) {
                $guest = $transaction->guest->member_code ?? '';
                return '<span class="label label-success">'. $guest .'</spa>';
            })
            ->editColumn('user_name', function ($transaction) {
                return $transaction->user->name ?? '';
            })
            ->addColumn('action', function ($transaction) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('transaction.show', $transaction->transaction_id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('transaction.destroy', $transaction->transaction_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action', 'member_code'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        $transaction = Transaction::where('is_temp', true)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            $transaction = new Transaction();
            $transaction->guest_id = null;
            $transaction->total_item_quantity = 0;
            $transaction->total_price = 0;
            $transaction->final_price = 0;
            $transaction->money_received = 0;
            $transaction->user_id = auth()->id();
            $transaction->is_temp = true;
            $transaction->save();
        }

        session(['transaction_id' => $transaction->transaction_id]);
        return redirect()->route('transaction_detail.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $transaction = Transaction::findOrFail($request->transaction_id);
        $transaction->update([
            'guest_id' => $request->guest_id,
            'total_item_quantity' => $request->total_item_quantity,
            'total_price' => $request->total_price,
            'final_price' => $request->final_price,
            'money_received' => $request->money_received,
            'is_temp' => false,
        ]);

        foreach (TransactionDetail::where('transaction_id', $transaction->transaction_id)->get() as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock', $item->quantity);
            }
        }

        return redirect()->route('transaction.selesai');
    }

    public function show($id)
    {
        $detail = TransactionDetail::with('product')->where('transaction_id', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('code', function ($detail) {
                return '<span class="label label-success">'. $detail->product->code .'</span>';
            })
            ->addColumn('name', function ($detail) {
                return $detail->product->name;
            })
            ->addColumn('sell_price', function ($detail) {
                return 'Rp. '. money_number_format($detail->sell_price);
            })
            ->addColumn('quantity', function ($detail) {
                return money_number_format($detail->quantity);
            })
            ->addColumn('price', function ($detail) {
                return 'Rp. '. money_number_format($detail->price);
            })
            ->rawColumns(['code'])
            ->make(true);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            foreach (TransactionDetail::where('transaction_id', $transaction->transaction_id)->get() as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
                $item->delete();
            }
            $transaction->delete();
            return response(null, 204);
        }
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    public function selesai()
    {
        return view('transaction.selesai');
    }

    public function notaKecil()
    {
        $transaction = Transaction::find(session('transaction_id'));
        if (! $transaction) {
            abort(404);
        }
        $detail = TransactionDetail::with('product')
            ->where('transaction_id', session('transaction_id'))
            ->get();

        return view('transaction.nota_kecil', compact('transaction', 'detail'));
    }

    public function notaBesar()
    {
        $transaction = Transaction::find(session('transaction_id'));
        if (! $transaction) {
            abort(404);
        }
        $detail = TransactionDetail::with('product')
            ->where('transaction_id', session('transaction_id'))
            ->get();

        $pdf = PDF::loadView('transaction.nota_besar', compact('transaction', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('transaction-'. date('Y-m-d-his') .'.pdf');
    }

    public function getTransactionByDate(Request $request): \Illuminate\Http\JsonResponse
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $startDate = (new DateTime())->setDate($year, $month, 1)->format('Y-m-d');
        $endDate = (new DateTime())->setDate($year, $month, 1)->modify('last day of this month')->format('Y-m-d');
        $endDate = ($endDate > date('Y-m-d')) ? date('Y-m-d') : $endDate;

        $dates = [];
        $incomes = [];

        Log::info('API Response');
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $day = date('j', strtotime($currentDate));;
            $totalIncome = Transaction::whereDate('created_at', $currentDate)->sum('final_price');
            $incomes[] = $totalIncome;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        $startDateFormatted = to_date_string($startDate, false);
        $endDateFormatted = to_date_string($endDate, false);
        return response()->json([
            'dates' => $dates,
            'incomes' => $incomes,
            'startDate' => $startDateFormatted,
            'endDate' => $endDateFormatted
        ]);
    }
}
