<?php

namespace App\Http\Controllers\API\Auth;

use App\Jobs\Auth\RegisteredJob;
use App\Services\RequestResponseService;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use App\Services\SecureShellKeyService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Register default action
     *
     * @param AuthRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(AuthRegisterRequest $request)
    {
        $user = $this->createUser($request);

        if ($user->save()) {
            $user->assignRole('USER');

            $this->registered($user);

            return RequestResponseService::response(
                config('message.10-200'),
                '10-200',
                200
            );
        }
    }

    /**
     * Create User
     *
     * @param Request $request
     * @return User
     */
    protected function createUser(Request $request)
    {
        return new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activated_token' => Str::random(60),
            'is' => 'pending',
        ]);
    }

    /**
     * User Authenticated
     *
     * @param User $user
     */
    protected function registered(User $user)
    {
        RegisteredJob::dispatch($user)->onQueue('jobs');
    }
}
