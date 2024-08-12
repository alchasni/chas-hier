<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use PDF;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('member.index');
    }

    public function data()
    {
        $guest = Guest::orderBy('created_at')->get();

        return datatables()
            ->of($guest)
            ->addIndexColumn()
            ->addColumn('select_all', function ($product) {
                return '
                    <input type="checkbox" name="id_member[]" value="'. $product->id_member .'">
                ';
            })
            ->addColumn('created_at', function ($guest) {
                return '<span class="label label-success">'. $guest->created_at .'<span>';
            })
            ->addColumn('aksi', function ($guest) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('member.update', $guest->id_member) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('member.destroy', $guest->id_member) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'select_all', 'created_at'])
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $guest = Guest::latest()->first() ?? new Guest();
        $guest = new Guest();
        $guest->nama = $request->nama;
        $guest->telepon = $request->telepon;
        $guest->alamat = $request->alamat;
        $guest->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $guest = Guest::find($id);

        return response()->json($guest);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $guest = Guest::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guest = Guest::find($id);
        $guest->delete();

        return response(null, 204);
    }

    public function cetakMember(Request $request)
    {
        $datamember = collect(array());
        foreach ($request->id_member as $id) {
            $guest = Guest::find($id);
            $datamember[] = $guest;
        }

        $datamember = $datamember->chunk(2);

        $no  = 1;
        $pdf = PDF::loadView('member.cetak', compact('datamember', 'no'));
        $pdf->setPaper(array(0, 0, 566.93, 850.39), 'potrait');
        return $pdf->stream('member.pdf');
    }
}
