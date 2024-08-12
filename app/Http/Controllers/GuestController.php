<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
    public function data()
    {
        $guest = Guest::orderBy('created_at')->get();

        return datatables()
            ->of($guest)
            ->addIndexColumn()
            ->addColumn('select_all', function ($guest) {
                return '
                    <input type="checkbox" name="guest_id[]" value="'. $guest->guest_id .'">
                ';
            })
            ->addColumn('action', function ($guest) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="updateOne(`'. route('guest.update', $guest->guest_id) .'`)" class="btn btn-xs btn-info btn-flat">
                        <i class="fa fa-pencil"> edit</i>
                    </button>
                    <button type="button" onclick="deleteOne(`' . route('guest.destroy', $guest->guest_id) . '`, `' . $guest->name . '`)" class="btn btn-xs btn-danger btn-flat">
                        <i class="fa fa-trash"> delete</i>
                    </button>
                </div>
                ';
            })
            ->rawColumns(['action', 'select_all', 'created_at'])
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
        $guest = Guest::latest()->first() ?? new Guest();
        $request['member_code'] = 'M'. add_zero((int)$guest->guest_id +1, 6);
        return $this->saveModel($request, new Guest());
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
        $guest = Guest::find($id);
        if (!$guest) {
            return response()->json('Error: Guest not found.', 404);
        }
        return $this->saveModel($request, $guest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $guest = Guest::find($id);
        $guest->delete();

        return response(null, 204);
    }
}
