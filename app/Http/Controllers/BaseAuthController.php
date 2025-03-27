<?php

namespace App\Http\Controllers;

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
}
