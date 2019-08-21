<?php

namespace App\Http\Controllers\API\Auth;

use App\Jobs\Auth\ResetPasswordJob;
use App\Services\RequestResponseService;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /**
     * Reset password default action
     *
     * @param AuthResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(AuthResetPasswordRequest $request)
    {
        $passwordReset = PasswordReset::where([
            ['email', '=', $request->get('email')],
            ['token', '=', $request->get('token')],
        ])->take(1);

        if (! $passwordReset) {
            return RequestResponseService::badRequest(
                config('message.10-404'),
                '10-404'
            );
        }

        if ($this->userUpdate($request)) {
            $passwordReset->delete();
            return $this->resetPasswordResponse();
        }
    }

    /**
     * Reset password response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function resetPasswordResponse()
    {
        return RequestResponseService::response(
            config('message.21-200'),
            '21-200',
            200
        );
    }

    /**
     * User Update
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function userUpdate(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        if (! $user) {
            return RequestResponseService::notFoundRequest(
                config('message.13-404'),
                '13-404'
            );
        }

        $user->password = bcrypt($request->password);

        if ($user->save()) {
            $this->resetPasswordEvent($user);

            return true;
        }

        return false;
    }

    /**
     * Reset password event
     *
     * @param User $user
     */
    protected function resetPasswordEvent(User $user)
    {
        ResetPasswordJob::dispatch($user)->onQueue('jobs');
    }
}
