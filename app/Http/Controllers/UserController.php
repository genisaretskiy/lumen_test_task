<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    private Request $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register(UserService $userService)
    {
        $valid = $this->validate($this->request, $userService->getUserValidateConfig());
        if (!$valid) {
            return new HttpException(400, implode(',', $valid));
        }
        return response()->json($userService->register($this->request->all()));
    }

    /**
     * @param UserService $userService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function signIn(UserService $userService)
    {
        if (!$this->validate($this->request, $userService->getSignInValidateConfig())) {
            return response()->json(['status' => 'fail'], 401);
        }
        $authToken = $userService->login($this->request['email'], $this->request['password']);
        if (!empty($authToken)) {
            return response()->json(['status' => 'success', 'api_token' => $authToken]);
        }
        return response()->json(['status' => 'fail'], 401);
    }

    public function recoverPassword(UserService $userService)
    {
        if (!$this->validate($this->request, $userService->getResetPasswordValidateConfig())) {
            return response()->json(['status' => 'fail'], 401);
        }
        $token = $userService->resetPassword($this->request->input('email'));

        return response()->json(['status' => 'success', 'reset_token' => $token]);
    }

    public function getCompanies(UserService $userService, CompanyService $companyService)
    {
        return response()->json($companyService->getCompanies(Auth::user()));
    }

    public function addCompany(UserService $userService, CompanyService $companyService)
    {
        if (!$this->validate($this->request, $userService->getAddCompaniesValidateConfig())) {
            return response()->json(['status' => 'fail'], 401);
        }
        $companyService->addCompany(Auth::user(), $this->request['companies']);

        return response()->json(['status' => 'success']);
    }

    private function getResponseByException(HttpException $exception)
    {
        return response()->json(['status' => 'fail', 'message' => $exception->getMessage()], $exception->getCode());
    }
}
