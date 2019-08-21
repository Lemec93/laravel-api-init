<?php

namespace App\Http\Controllers\API\Auth;

use App\Jobs\Auth\ForgotPasswordJob;
use App\Services\RequestResponseService;
use App\Services\UserCheckingService;
use App\Http\Requests\AuthForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    /**
     * Forgot password default action
     *
     * @param AuthForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(AuthForgotPasswordRequest $request)
    {
        UserCheckingService::processing($request);

        $user = User::where('email', $request->email)->first();

        return $this->createResetToken($user);
    }

    /**
     * Create reset Token
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createResetToken(User $user)
    {
        $passwordReset = PasswordReset::firstOrCreate(
            ['email' => $user->email],
            [
                'token' => Str::random(60),
                'created_at' => now(),
            ]
        );

        if ($user && $passwordReset) {
            $this->forgotPassword($user, $passwordReset);

            return $this->forgotPasswordResponse();
        }
    }

    /**
     * Forgot password response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forgotPasswordResponse()
    {
        return RequestResponseService::response(
            config('message.12-200'),
            '12-200',
            200
        );
    }

    /**
     * Forgot password event
     *
     * @param User $user
     * @param PasswordReset $passwordReset
     */
    protected function forgotPassword(User $user, PasswordReset $passwordReset)
    {
        if (! app()->environment('testing')) {
            ForgotPasswordJob::dispatch($user, $passwordReset)->onQueue('jobs');
        }
    }
}
