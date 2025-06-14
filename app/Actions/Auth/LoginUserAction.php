<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    public static function execute(Request $request): string
    {
        $request->validate([
            'email' => 'string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException;
        }

        $user = User::where('email', $request->get('email'))->firstOrFail();

        return $user->createToken('api')->plainTextToken;
    }
}
