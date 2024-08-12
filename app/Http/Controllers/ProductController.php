<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $category = Category::all()->pluck('name', 'category_id');
        return view('product.index', compact('category'));
    }

    public function data()
    {
        $product = Product::leftJoin('category', 'category.category_id', '=', 'product.category_id')
            ->select('product.*', 'category.name as category_name')
            ->orderBy('product.code', 'asc')
            ->get();

        return datatables()
            ->of($product)
            ->addIndexColumn()
            ->addColumn('select_all', function ($product) {
                return '
                    <input type="checkbox" name="product_id[]" value="'. $product->product_id .'">
                ';
            })
            ->addColumn('code', function ($product) {
                return '<span class="label label-success">'. $product->code .'</span>';
            })
            ->addColumn('buy_price', function ($product) {
                return money_number_format($product->buy_price);
            })
            ->addColumn('sell_price', function ($product) {
                return money_number_format($product->sell_price);
            })
            ->addColumn('stock', function ($product) {
                return money_number_format($product->stock);
            })
            ->addColumn('action', function ($product) {
                $actions = '
                <div class="btn-group">
                ';
                if (auth()->user()->level == 1) {
                    $actions .= '
                        <button type="button" onclick="updateOne(`' . route('product.update', $product->product_id) . '`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-pencil"> edit</i>
                        </button>
                    ';
                }
                if (auth()->user()->level == 1) {
                    $actions .= '
                        <button type="button" onclick="deleteOne(`' . route('product.destroy', $product->product_id) . '`, `' . $product->name . '`)" class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"> delete</i>
                        </button>
                    ';
                }
                if (auth()->user()->level != 2) {
                    $actions .= '
                        <button type="button" onclick="updateStock(`' . route('product.update', $product->product_id) . '`, `' . $product->name . '`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-cubes"> update stock</i>
                        </button>
                    ';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action', 'code', 'select_all'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $product = Product::latest()->first() ?? new Product();
        $request['code'] = 'P'. add_zero((int)$product->product_id +1, 6);
        return $this->saveModel($request, new Product());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id)
    {
        //
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
        $product = Product::find($id);
        if (!$product) {
            return response()->json('Error: Product not found.', 404);
        }
        return $this->saveModel($request, $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $product = Product::find($id);
        $product->delete();

        return response(null, 204);
    }

    public function printBarcode(Request $request)
    {
        $productData = [];
        foreach ($request->product_id as $id) {
            $product = Product::find($id);
            $productData[] = $product;
        }

        $pdf = PDF::loadView('product.barcode', compact('productData'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('product.pdf');
    }
}
