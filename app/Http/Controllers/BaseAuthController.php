<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\RoleEnum;

class BaseAuthController extends Controller
{
    protected string $role;
    protected array $relations = [];

    public function __construct()
    {
        $this->role = RoleEnum::CUSTOMER->value;
    }

    public function register(RegisterUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->assignRole($this->role);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = auth()->login($user);
        /** @noinspection PhpParamsInspection */
        $refreshToken = auth()->refresh();
        return rest()
            ->ok()
            ->message(__('site.registered_successfully'))
            ->data([
                'user' => UserResource::make($user->load($this->relations)),
                'token' => $token,
                'refresh_token' => $refreshToken,
            ])->send();
    }

    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if (!$token) {
            return rest()
                ->notAuthorized()
                ->message(__('site.invalid_credentials'))
                ->send();
        }

        $user = auth()->user();
        if (!$user->hasRole($this->role)) {
            return rest()
                ->notAuthorized()
                ->message(__('site.invalid_credentials'))
                ->send();
        }

        /** @noinspection PhpParamsInspection */
        $refreshToken = auth()->refresh();
        return rest()
            ->ok()
            ->message(__('site.logged_in_successfully'))
            ->data([
                'user' => UserResource::make($user->load($this->relations)),
                'token' => $token,
                'refresh_token' => $refreshToken,
            ])->send();
    }

    public function logout()
    {
        auth()->logout();
        return rest()
            ->ok()
            ->message(__('site.logged_out_successfully'))
            ->send();
    }

    public function refreshToken()
    {
        /** @noinspection PhpParamsInspection */
        return rest()
            ->ok()
            ->message(__('site.refresh_token_successfully'))
            ->data([
                'user' => UserResource::make(auth()->user()->load($this->relations)),
                'token' => auth()->setTTL(config('jwt.ttl'))->refresh(),
                'refresh_token' => auth()->setTTL(config('jwt.refresh_ttl'))->refresh(),
            ]);
    }
}
