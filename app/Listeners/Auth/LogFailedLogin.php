<?php

namespace App\Listeners\Auth;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogFailedLogin implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Log::channel('rico_auth')
           ->notice(
               'USER_FAILED_AUTH',
               [$event]
           );
    }
}
