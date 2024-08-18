<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends BaseController
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
    public function data(): JsonResponse
    {
        try {
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
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load data'], 500);
        }
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
        $validatedData = $request->validate([
            'total_item_quantity' => 'required|integer|min:0',
            'total_price' => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'money_received' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        $details = TransactionDetail::where('transaction_id', $transaction->transaction_id)->get();
        foreach ($details as $item) {
            $product = Product::find($item->product_id);
            if ($product && $product->stock < $item->quantity) {
                return redirect()->back()->withErrors(['error' => 'Product stock is insufficient for product: ' . $product->name]);
            }
        }

        $transaction->fill($validatedData);
        $transaction->is_temp = false;
        $this->saveModel($validatedData, $transaction);

        $details = TransactionDetail::where('transaction_id', $transaction->transaction_id)->get();
        foreach ($details as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock -= $item->quantity;
                $product->save();
            }
        }

        return redirect()->route('transaction.created');
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
            $details = TransactionDetail::where('transaction_id', $transaction->transaction_id)->get();
            foreach ($details as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
                $item->delete();
            }
            $transaction->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    public function created()
    {
        return view('transaction.created');
    }

    public function printOrders()
    {
        $transaction = Transaction::find(session('transaction_id'));
        if (! $transaction) {
            abort(404);
        }
        $detail = TransactionDetail::with('product')
            ->where('transaction_id', session('transaction_id'))
            ->get();

        return view('transaction.print_orders', compact('transaction', 'detail'));
    }

    public function printOrdersPDF()
    {
        $transaction = Transaction::find(session('transaction_id'));
        if (! $transaction) {
            abort(404);
        }
        $detail = TransactionDetail::with('product')
            ->where('transaction_id', session('transaction_id'))
            ->get();

        $pdf = PDF::loadView('transaction.print_orders_pdf', compact('transaction', 'detail'));
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
