<?php

namespace App\Actions\Auth;

use App\Enums\System\RoleEnum;
use App\Enums\User\UserTypeEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public static function execute(Request $request): string
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255|regex:/^\+?[0-9]{1,4}?[0-9]{7,14}$/',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            ...$request->only(['name', 'email', 'phone']),
            'password' => Hash::make($request->get('password')),
            'type' => UserTypeEnum::USER,
        ]);

        $user->assignRole([RoleEnum::USER]);

        $token = $user->createToken('api')->plainTextToken;

        event(new Registered($user));

        Auth::login($user);

        return $token;
    }
}
