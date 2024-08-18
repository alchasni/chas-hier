<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    /**
     * @throws Exception
     */
    public function data(): JsonResponse
    {
        try {
        $products = Product::leftJoin('category', 'category.category_id', '=', 'product.category_id')
            ->select('product.*', 'category.name as category_name')
            ->orderBy('product.code', 'asc')
            ->get();

        return datatables()
            ->of($products)
            ->addIndexColumn()
            ->addColumn('select_all', function ($products) {
                return '
                    <input type="checkbox" name="product_id[]" value="'. $products->product_id .'">
                ';
            })
            ->addColumn('code', function ($products) {
                return '<span class="label label-success">'. $products->code .'</span>';
            })
            ->addColumn('buy_price', function ($products) {
                return money_number_format($products->buy_price);
            })
            ->addColumn('sell_price', function ($products) {
                return money_number_format($products->sell_price);
            })
            ->addColumn('stock', function ($products) {
                return money_number_format($products->stock);
            })
            ->addColumn('action', function ($products) {
                $actions = '
                <div class="btn-group">
                ';
                if (auth()->user()->level == 1) {
                    $actions .= '
                        <button type="button" onclick="updateOne(`' . route('product.update', $products->product_id) . '`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-pencil"> edit</i>
                        </button>
                    ';
                    $actions .= '
                        <button type="button" onclick="deleteOne(`' . route('product.destroy', $products->product_id) . '`, `' . $products->name . '`)" class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"> delete</i>
                        </button>
                    ';
                }
                if (auth()->user()->level != 2) {
                    $actions .= '
                        <button type="button" onclick="updateStock(`' . route('product.update', $products->product_id) . '`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-cubes"> update stock</i>
                        </button>
                    ';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action', 'code', 'select_all'])
            ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load data'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:category,category_id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::latest()->first() ?? new Product();
        $validatedData['code'] = 'P'. add_zero((int)$product->product_id +1, 6);

        return $this->saveModel($validatedData, new Product());
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param bool $isStock
     * @return JsonResponse
     */
    public function update(Request $request, int $id, bool $isStock = false): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:category,category_id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
        ];
        $validatedData = $request->validate($rules);

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return $this->saveModel($validatedData, $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    public function printBarcode(Request $request)
    {
        $productData = Product::whereIn('product_id', $request->product_id)->get();
        $pdf = PDF::loadView('product.barcode', compact('productData'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('product.pdf');
    }
}
