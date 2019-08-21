<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Requests\AuthValidateAccountRequest;
use App\Jobs\Auth\ActivatedAccountJob;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\RequestResponseService;
use App\Http\Controllers\Controller;

class ActiveAccountController extends Controller
{
    /**
     * Reset password default action
     *
     * @param AuthValidateAccountRequest $request
     *
     * @return mixed
     */
    public function __invoke(AuthValidateAccountRequest $request)
    {
        return $this->activatedUserAccount($request);
    }

    /**
     * Activated user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function activatedUserAccount(Request $request)
    {
        $user = User::where(
            'activated_token',
            '=',
            $request->get('token')
        )->first();

        if (! $user) {
            return RequestResponseService::notFoundRequest(
                config('message.14-404'),
                '14-404'
            );
        }

        return $this->userUpdate($user);
    }

    /**
     * User update
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function userUpdate(User $user)
    {
        $user->activated_token = null;
        $user->is = 'activated';

        if (! $user->save()) {
            return RequestResponseService::badRequest(
                config('message.10-400'),
                '10-400'
            );
        }
        // Dispatching account
        ActivatedAccountJob::dispatch($user);
        // Request response service
        return RequestResponseService::response(
            config('message.13-200'),
            '13-200',
            200
        );
    }
}
