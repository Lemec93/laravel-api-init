<?php

namespace App\Http\Controllers\API\Auth;

use App\Jobs\Auth\LogoutJob;
use App\Services\RequestResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Logout default action
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->token()->revoke()) {
            LogoutJob::dispatch($request->user());

            return RequestResponseService::response(
                'You have been successfully disconnected.',
                '11-200',
                200
            );
        }
    }
}
