<?php

namespace App\Jobs\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Auth\AuthenticatedNotification;

class AuthenticatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // User authenticated log
        Log::channel('rico_auth')
           ->notice(
               'USER_AUTHENTICATED',
               ['id' => $this->user->id]
           );

        // User notify
        if (is_null($this->user->setting()->whereJsonContains(
            'settings->notifications->login',
            true
        )->get())) {
            $this->user->notify((
                new AuthenticatedNotification($this->user)
            )->onQueue('notifications'));
        }
    }
}
