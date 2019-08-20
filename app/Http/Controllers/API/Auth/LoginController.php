<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Resources\UserResource;
use App\Jobs\Auth\AuthenticatedJob;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\UserCheckingService;
use App\Exceptions\UserLoginErrorException;

class LoginController extends Controller
{
    /**
     * Login default action
     *
     * @param Request $request
     * @return mixed
     */
    public function __invoke(AuthLoginRequest $request)
    {
        UserCheckingService::processing($request);

        if (! $this->attemptLogin($request)) {
            throw new UserLoginErrorException();
        }

        return $this->userToken($request);
    }

    /**
     * Set User Token
     *
     * @param Request $request
     * @return mixed
     */
    protected function userToken(Request $request)
    {
        $user = $request->user();

        $tokenResult = $this->createUserToken($user);

        $token = $tokenResult->token;

        $this->rememberUser($request, $token);

        if ($token->save()) {
            //$this->authenticated($user);

            return $this->sendLoginResponse($request, $tokenResult);
        }
    }

    /**
     * Send Login Response
     *
     * @param Request $request
     * @param $tokenResult
     * @return object
     */
    protected function sendLoginResponse(Request $request, $tokenResult)
    {
        return (new UserResource($request->user()))
            ->additional([
                'meta' => [
                    'token_type' => 'Bearer',
                    'expires_at' =>
                        Carbon::parse($tokenResult->token->expires_at)
                            ->toDateTimeString(),
                    'access_token' => $tokenResult->accessToken,
                ],
            ]);
    }

    /**
     * Create user token
     *
     * @param User $user
     * @return mixed
     */
    protected function createUserToken(User $user)
    {
        return $user->createToken('Personal access token', ['*']);
    }

    /**
     * Set Remember
     *
     * @param Request $request
     * @param $token
     */
    protected function rememberUser(Request $request, $token)
    {
        if ($request->get('remember_me')) {
            $token->expires_at = now()->addMonths(12);
        }
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            array_merge(
                $this->credentials($request),
                ['deleted_at' => null]
            ),
            (bool) $request->filled('remember_me')
        );
    }

    /**
     * Credentials
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /**
     * User Authenticated
     *
     * @param User $user
     */
    protected function authenticated(User $user)
    {
        if (! app()->environment('testing')) {
            AuthenticatedJob::dispatch($user)->onQueue('jobs');
        }
    }
}
