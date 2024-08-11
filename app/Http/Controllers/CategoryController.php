<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
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

    public function data()
    {
        $category = Category::orderBy('category_id', 'desc')->get();

        return datatables()
            ->of($category)
            ->addIndexColumn()
            ->addColumn('action', function ($category) {
                return '
                <div class="btn-group">
                    <button onclick="editForm(`'. route('category.update', $category->category_id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"> edit</i></button>
                    <button onclick="deleteData(`' . route('category.destroy', $category->category_id) . '`, `' . $category->name . '`)" class="btn btn-xs btn-danger btn-flat">
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
     * @return \Illuminate\Http\Response
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
        return $this->saveCategory($request, new Category());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $category = Category::find($id);

        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json('Error: Category not found.', 404);
        }

        return $this->saveCategory($request, $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();

        return response(null, 204);
    }

    private function saveCategory(Request $request, Category $category): JsonResponse
    {
        try {
            $category->name = $request->name;
            $category->save();

            return response()->json('Category data saved', 200);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json('Error: Duplicate entry. The category name already exists.', 409);
            }
            return response()->json('Error: Could not save the data. Please try again.', 500);
        }
    }
}
