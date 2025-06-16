<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $currentUser = User::find(Auth::id());

        $users = User::all();

        if ($currentUser->role->value == "admin") {
            return response()->json($users);
        }

        // Para não admin, ocultar cpf e phone
        $usersFiltered = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                // cpf e phone omitidos
            ];
        });

        return response()->json($usersFiltered);
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
        $currentUser = User::find(Auth::id());
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        // Se o usuário não for admin, oculta cpf e phone
        if (!$currentUser || $currentUser->role !== 'admin') {
            $user = $user->only([
                'id',
                'name',
                'email',
                'role',
                'created_at',
                'updated_at',
            ]);
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
     *             @OA\Property(property="role", type="string", example="user"),
     *             @OA\Property(property="avatar", type="string", format="url", example="https://supabase.co/storage/v1/object/public/user-images/avatar.jpg", description="Remote avatar image URL")
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
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid image URL provided"
     *     )
     * )
     */
    public function update(Request $request)
    {
        $user = User::find($request->route('id'));

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'cpf' => [
                'nullable',
                'string',
                'max:14',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => [
                'required',
                'string',
                Rule::in(UserRole::values()),
            ],
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|string|url|max:500', // Changed to accept URL
        ]);

        // Handle avatar URL (remote URL from Supabase or other services)
        if ($request->filled('avatar')) {
            if ($this->isValidImageUrl($request->avatar)) {
                // If user had a local avatar file, remove it since we're switching to URL
                if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store the remote URL directly
                $validatedData['avatar'] = $request->avatar;
            } else {
                return response()->json([
                    'message' => 'Invalid image URL provided',
                    'errors' => ['avatar' => ['The provided URL is not a valid image URL']]
                ], 422);
            }
        } else {
            unset($validatedData['avatar']);
        }

        // Update the user in the database
        $user->update($validatedData);

        return response()->json($user->fresh());
    }

    /**
     * Validate if the provided URL is a valid image URL
     */
    private function isValidImageUrl(string $url): bool
    {
        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if URL has valid image extension or is from trusted domains
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $allowedDomains = [
            'supabase.co',
            'amazonaws.com',
            'cloudinary.com',
            'imgur.com',
            'gravatar.com',
            'ui-avatars.com'
        ];

        $urlParts = parse_url($url);
        $host = $urlParts['host'] ?? '';

        // Check if it's from an allowed domain
        foreach ($allowedDomains as $domain) {
            if (str_contains($host, $domain)) {
                return true;
            }
        }

        // Check file extension
        $path = $urlParts['path'] ?? '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, $allowedExtensions);
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
