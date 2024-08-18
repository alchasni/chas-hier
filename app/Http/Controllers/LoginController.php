<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class LoginController extends AuthenticatedSessionController
{
    public function store(LoginRequest $request)
    {
        $response = parent::store($request);

        $user = User::find(auth()->user()->id);
        $user->session_token = session()->getId();
        $user->update();

        return $response;
    }
}
