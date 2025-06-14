<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;

class ResetPasswordAction
{
    public static function execute(Request $request): void
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->get('email'))->firstOrFail();
        $passwordResetToken = ValidateResetCodeAction::execute($request);

        $user->update([
            'password' => bcrypt($request->get('password')),
        ]);

        $passwordResetToken->delete();
    }
}
