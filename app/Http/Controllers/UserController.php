<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="UserAPI",
 *     description="Endpoints relacionados aos usuários"
 * )
 */

/**
 * @OA\Schema(
 *    schema="UserAPI",
 *    required={"id", "name", "email", "phone", "cpf", "role"},
 *    @OA\Property(property="id", type="integer", format="int64", description="ID do usuário"),
 *    @OA\Property(property="name", type="string", description="Nome do usuário"),
 *    @OA\Property(property="email", type="string", format="email", description="Email do usuário"),
 *    @OA\Property(property="phone", type="string", description="Telefone do usuário"),
 *    @OA\Property(property="cpf", type="string", description="CPF do usuário"),
 *    @OA\Property(property="role", type="string", description="Função do usuário"),
 *    @OA\Property(property="created_at", type="string", format="date-time", description="Data de criação do usuário"),
 *    @OA\Property(property="updated_at", type="string", format="date-time", description="Data de atualização do usuário")
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"UserAPI"},
     *     summary="Lista todos os usuários",
     *     operationId="getUsers",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserAPI"))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"UserAPI"},
     *     summary="Obtém um usuário específico",
     *     operationId="getUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/UserAPI")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Acesso não autorizado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="updateUser",
     *     summary="Atualizar um usuário",
     *     tags={"UserAPI"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "role"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="phone", type="string", example="(11) 91234-5678"),
     *             @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/UserAPI")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'role' => 'required|string|in:user,admin,superadmin',
        ]);
        $user->update($data);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     operationId="deleteUser",
     *     summary="Deletar um usuário",
     *     tags={"UserAPI"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário deletado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}
