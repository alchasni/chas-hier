<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('user.index');
    }

    /**
     * @throws Exception
     */
    public function data()
    {
        try {
            $user = User::isLowerLevel(auth()->user()->level)->orderBy('id', 'desc')->get();

            return datatables()
                ->of($user)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                    return '
                <div class="btn-group">
                    <button type="button" onclick="updateOne(`'. route('user.update', $user->id) .'`)" class="btn btn-xs btn-info btn-flat">
                        <i class="fa fa-pencil"> edit</i>
                    </button>
                    <button type="button" onclick="deleteOne(`' . route('user.destroy', $user->id) . '`, `' . $user->name . '`)" class="btn btn-xs btn-danger btn-flat">
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'level' => 'required|integer|between:0,5',
        ]);

        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'level' => $validatedData['level'],
            'password' => Hash::make($validatedData['password']),
            'photo' => '/img/user.jpg',
        ];

        return $this->saveModel($data, new User());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        return response()->json($user);
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8',
            'level' => 'required|integer|between:0,5',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Use saveModel for saving the user
        return $this->saveModel($validatedData, $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'Successfully deleted the data'], 200);
        }
        return response()->json(['error' => 'User not found'], 404);
    }

    public function profile()
    {
        $profile = auth()->user();
        return view('user.profile', compact('profile'));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'old_password' => 'required_with:password|string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        if ($request->filled('password')) {
            if (!Hash::check($validatedData['old_password'], $user->password)) {
                return response()->json(['error' => 'Old password did not match'], 422);
            }
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $oldFile = $user->photo;

            $filename = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $filename);

            $user->photo = "/img/$filename";

            if ($oldFile && file_exists(public_path($oldFile))) {
                unlink(public_path($oldFile));
            }
        }

        return $this->saveModel($validatedData, $user);
    }
}
