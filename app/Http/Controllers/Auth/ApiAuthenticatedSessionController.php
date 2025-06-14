<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoadAuthUserAction;
use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\ResetPasswordAction;
use App\Actions\Auth\ValidateResetCodeAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticatedSessionController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // Register User
        $token = RegisterUserAction::execute($request);

        // LoadAuthenticatedUser
        $user = LoadAuthUserAction::execute();
        $user->token = $token;

        return response()->json([
            'data' => $user,
            'message' => __('Registro realizado com sucesso'),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        // Login User
        $token = LoginUserAction::execute($request);

        // LoadAuthenticatedUser
        $user = LoadAuthUserAction::execute();
        $user->token = $token;

        return response()->json([
            'data' => $user,
            'message' => __('Você fez login'),
        ]);
    }

    public function validate(Request $request): JsonResponse
    {
        return response()->json([
            'data' => LoadAuthUserAction::execute(),
            'message' => Auth::check() ? __('Você está autenticado') : __('Você não está autenticado'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('Você fez logout'),
        ]);
    }

    public function validateResetCode(Request $request): JsonResponse
    {
        ValidateResetCodeAction::execute($request);

        return response()->json([
            'message' => __('Recuperação de senha válida'),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        ResetPasswordAction::execute($request);

        return response()->json([
            'message' => __('Recuperação de senha realizada com sucesso'),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = User::findOrFail($request->user()->id);

        if (!password_verify($request->get('current_password'), $user->password)) {
            return response()->json([
                'message' => __('Senha atual incorreta'),
            ], 422);
        }

        $user->update(['password' => bcrypt($request->get('new_password'))]);

        return response()->json([
            'message' => __('Senha alterada com sucesso'),
        ]);
    }
}
