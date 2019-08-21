<?php

namespace App\Jobs\Auth;

use App\Models\User;
use App\Notifications\Auth\RegisteredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RegisteredJob implements ShouldQueue
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
        // Log registered
        Log::channel('rico_auth')
           ->notice(
               'USER_REGISTERED',
               [
                   'id' => $this->user->id,
                   'registered_at' => $this->user->created_at,
               ]
           );

        // Send notification
        $this->user->notify((
            new RegisteredNotification($this->user)
        )->onQueue('notifications'));
    }
}
