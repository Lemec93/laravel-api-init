<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\UserBannedException;
use App\Exceptions\UserDisabledException;
use App\Exceptions\UserLoginErrorException;

class UserCheckingService
{
    /**
     * Processing login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public static function processing(Request $request)
    {
        $user = User::whereEmail($request->get('email'))->first();

        if (! $user) {
            throw new UserLoginErrorException();
        }

        if ($user->isBanned()) {
            throw new UserBannedException();
        }

        if ($user->isDisabled()) {
            throw new UserDisabledException();
        }
    }
}
