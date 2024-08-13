<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateUserLoginSession implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
//        $user = User::find($event->user->id);
//        $user->session_token = session()->getId();
//        $user->update();
//
//        $user = User::find($event->user->id);
//        Log::info('Login : Current Session ID: ' . session()->getId());
//        Log::info('Login : Stored Session ID: ' . $user->session_token);
    }
}
