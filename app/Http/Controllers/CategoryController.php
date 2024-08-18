<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;

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
        try {
            $categories = Category::orderBy('category_id', 'desc')->get();

            return datatables()
                ->of($categories)
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
        ]);

        return $this->saveModel($validatedData, new Category());
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return $this->saveModel($validatedData, $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }
        return response()->json(['error' => 'Category not found'], 404);
    }
}
