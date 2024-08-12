<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('category.index');
    }

    /**
     * @throws Exception
     */
    public function data()
    {
        $category = Category::orderBy('category_id', 'desc')->get();

        return datatables()
            ->of($category)
            ->addIndexColumn()
            ->addColumn('action', function ($category) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="updateOne(`'. route('category.update', $category->category_id) .'`)" class="btn btn-xs btn-info btn-flat">
                        <i class="fa fa-pencil"> edit</i>
                    </button>
                    <button type="button" onclick="deleteOne(`' . route('category.destroy', $category->category_id) . '`, `' . $category->name . '`)" class="btn btn-xs btn-danger btn-flat">
                        <i class="fa fa-trash"> delete</i>
                    </button>
                </div>
                ';
            })
            ->rawColumns(['action'])
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
        return $this->saveModel($request, new Category());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::find($id);

        return response()->json($category);
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
        $category = Category::find($id);

        if (!$category) {
            return response()->json('Error: Category not found.', 404);
        }

        return $this->saveModel($request, $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $category = Category::find($id);
        $category->delete();

        return response(null, 204);
    }
}
