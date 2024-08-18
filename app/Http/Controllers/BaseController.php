<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;

abstract class BaseController extends Controller
{
    protected function saveModel(array $data, $model): JsonResponse
    {
        try {
            $model->fill($data);
            $model->save();

            return response()->json('Data saved successfully', 200);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json('Error: Duplicate entry.', 409);
            }
            return response()->json('Error: Could not save the data.', 500);
        }
    }
}

