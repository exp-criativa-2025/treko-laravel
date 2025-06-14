<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoadAuthUserAction
{
    public static function execute(): ?User
    {
        if (!Auth::check()) {
            return null;
        }

        return User::find(Auth::id());
    }
}
