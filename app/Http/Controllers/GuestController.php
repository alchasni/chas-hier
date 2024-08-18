<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('guest.index');
    }

    /**
     * @throws Exception
     */
    public function data(): JsonResponse
    {
        try {
            $guests = Guest::orderBy('created_at')->get();

            return datatables()
                ->of($guests)
                ->addIndexColumn()
                ->addColumn('select_all', function ($guest) {
                    return '
                    <input type="checkbox" name="guest_id[]" value="'. e($guest->guest_id) .'">
                ';
                })
                ->addColumn('action', function ($guest) {
                    return '
                    <div class="btn-group">
                        <button type="button" onclick="updateOne(`'. e(route('guest.update', $guest->guest_id)) .'`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-pencil"> edit</i>
                        </button>
                        <button type="button" onclick="deleteOne(`' . e(route('guest.destroy', $guest->guest_id)) . '`, `' . e($guest->name) . '`)" class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"> delete</i>
                        </button>
                    </div>
                ';
                })
                ->rawColumns(['action', 'select_all'])
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
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $guest = Guest::latest()->first() ?? new Guest();
        $validatedData['member_code'] = 'M'. add_zero((int)$guest->guest_id + 1, 6);

        return $this->saveModel($validatedData, new Guest());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $guest = Guest::find($id);
        return response()->json($guest);
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
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $guest = Guest::find($id);
        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], 404);
        }

        return $this->saveModel($validatedData, $guest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $guest = Guest::find($id);
        if ($guest) {
            $guest->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }
        return response()->json(['error' => 'Guest not found'], 404);
    }
}
