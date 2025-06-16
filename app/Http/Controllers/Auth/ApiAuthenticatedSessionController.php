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

/**
 * @OA\Info(
 * title="Auth",
 * version="1.0"
 * )
 */

/**
 * @OA\Tag(
 * name="Auth",
 * description="Operações de registro relacionadas à autenticação de usuários"
 * )
 *
 * @OA\Schema(
 * schema="User",
 * type="object",
 * title="User",
 * required={"id", "name", "email", "token"},
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="John Doe"),
 * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 * @OA\Property(property="cpf", type="string", example="123.456.789-00"),
 * @OA\Property(property="phone", type="string", example="+55 11 91234-5678"),
 * @OA\Property(property="password", type="string", format="password", example="password123"),
 * )
 */

class ApiAuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"UserAPI"},
     *     summary="Registrar um novo usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="password", type="string", example="senha_secreta"),
     *             @OA\Property(property="password_confirmation", type="string", example="senha_secreta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/UserAPI")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro de validação.")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        // Register User
        $token = RegisterUserAction::execute($request);

        // LoadAuthenticatedUser
        $user = LoadAuthUserAction::execute();
        $user->token = $token;

        return response()->json([
            'data' => $user,
            'status' => 201,
            'message' => __('Registro realizado com sucesso'),
        ]);
    }


    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Login"},
     * summary="Realiza o login do usuário",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * ref="#/components/schemas/User"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Usuário autenticado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/User"),
     * @OA\Property(property="message", type="string", example="Você fez login")
     * )
     * )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/api/logout",
     * tags={"Logout"},
     * summary="Realiza o logout do usuário",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Usuário desconectado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Você fez logout")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Não autorizado",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Não autorizado")
     * )
     * )
     * )
     */
    public function logout(Request $request): JsonResponse
    {

        $user = $request->user();

        $user->currentAccessToken()->delete();

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
