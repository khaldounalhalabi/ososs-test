<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\RequestToResetPassword;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Mail\ResetPasswordCodeEmail;
use App\Models\User;
use App\Models\VerificationCode;
use App\RoleEnum;
use Illuminate\Support\Facades\Mail;

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

    public function requestToResetPassword(RequestToResetPassword $request)
    {
        $user = User::where('email', $request->validated('email'))->first();
        if (!$user) {
            return rest()
                ->notFound()
                ->message(__('site.invalid_email'))
                ->send();
        }

        do {
            $code = sprintf('%06d', mt_rand(1, 999999));
            $tempCode = VerificationCode::where('code', $code)->first();
        } while ($tempCode != null);

        VerificationCode::where('is_valid', true)
            ->where('user_id', $user->id)
            ->update(['is_valid' => false]);

        $verificationCode = VerificationCode::create([
            'code' => $code,
            'user_id' => $user->id,
            'valid_until' => now()->addHours(3)
        ]);

        Mail::to($user)
            ->send(new ResetPasswordCodeEmail($user, $verificationCode));

        return rest()
            ->ok()
            ->message(__("site.reset_password_code_sent"))
            ->send();
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $verificationCode = VerificationCode::where('code', $request->validated('code'))->first();
        if (!$verificationCode) {
            return rest()
                ->notFound()
                ->message(__('site.invalid_code'))
                ->send();
        }

        $user = User::where('email', $request->validated('email'))->first();
        if (!$user) {
            return rest()
                ->notFound()
                ->message(__('site.invalid_email'))
                ->send();
        }

        if ($verificationCode->valid_until->isPast()
            || !$verificationCode->is_valid
            || $verificationCode->user_id != $user->id
        ) {
            return rest()
                ->notFound()
                ->message(__('site.invalid_code'))
                ->send();
        }

        $user->update([
            'password' => $request->validated('password')
        ]);

        return rest()
            ->ok()
            ->data(true)
            ->message(__('site.password_changed'))
            ->send();
    }
}
