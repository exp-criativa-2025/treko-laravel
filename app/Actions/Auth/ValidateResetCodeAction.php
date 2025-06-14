<?php

namespace App\Actions\Auth;

use App\Models\PasswordResetToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ValidateResetCodeAction
{
    public static function execute(Request $request): PasswordResetToken
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'email' => 'string|email|max:255|exists:users,email',
        ]);

        $passwordResetToken = PasswordResetToken::where('email', $request->get('email'))
            ->orderByDesc('created_at')
            ->first();

        if (!$passwordResetToken) {
            throw new Exception(__('system.passwords.token'));
        }

        if (!Hash::check($request->get('code'), $passwordResetToken->token)) {
            throw new Exception(__('system.passwords.invalid_code'));
        }

        return $passwordResetToken;
    }
}
