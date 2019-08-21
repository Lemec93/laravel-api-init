<?php

namespace App\Jobs\Auth;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ForgotPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PasswordReset
     */
    private $password_reset;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param PasswordReset $password_reset
     */
    public function __construct(User $user, PasswordReset $password_reset)
    {
        $this->user = $user;
        $this->password_reset = $password_reset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log auth
        Log::channel('rico_auth')->notice(
            'USER_FORGOT_PASSWORD_REQUEST',
            [
                'id' => $this->user->id,
                'token' => $this->password_reset->token,
                'request_at' => $this->password_reset->created_at,
            ]
        );

        // Reset password notify
        $this->user->notify((
            new ResetPasswordNotification($this->user)
        )->onQueue('notifications'));
    }
}
