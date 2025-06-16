<?php

namespace App\Http\Controllers;

use App\Models\AcademicEntity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Info(
 * title="API Academic Entities", 
 * version="1.0"
 * )
 */
/**
 * @OA\Tag(
 *     name="Academic Entities",
 *     description="Operações relacionadas a entidades acadêmicas"
 * )
 *
 *  @OA\Schema(
 *     schema="AcademicEntity",
 *     type="object",
 *     title="Academic Entity",
 *     required={"type", "fantasy_name", "cnpj", "foundation_date", "status", "cep", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", enum={"school", "university", "research_center"}, example="university"),
 *     @OA\Property(property="fantasy_name", type="string", example="Universidade Exemplo"),
 *     @OA\Property(property="cnpj", type="string", example="12.345.678/0001-90"),
 *     @OA\Property(property="foundation_date", type="string", format="date", example="2020-01-01"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "pending"}, example="active"),
 *     @OA\Property(property="cep", type="string", example="12345-678"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class AcademicEntityController extends Controller
{
    public readonly AcademicEntity $academicEntity;

    public function __construct()
    {
        $this->academicEntity = new AcademicEntity();
    }
        /**
     * @OA\Get(
     *     path="/api/academic-entities",
     *     tags={"Academic Entities"},
     *     summary="Lista todas as entidades acadêmicas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de entidades acadêmicas"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $academicEntities = AcademicEntity::all();
        return response()->json($academicEntities, 200);  

    }

     /**
     * @OA\Post(
     *     path="/api/academic-entities",
     *     tags={"Academic Entities"},
     *     summary="Cria uma nova entidade acadêmica",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="type", type="string", example="university"),
     *             @OA\Property(property="fantasy_name", type="string", example="Minha Universidade"),
     *             @OA\Property(property="cnpj", type="string", example="12.345.678/0001-90"),
     *             @OA\Property(property="foundation_date", type="string", format="date", example="2020-01-01"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="cep", type="string", example="12345-678"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Entidade criada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {   
        $representativeUser = User::find(Auth::id());

        if ($representativeUser->role->value !== 'representative') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }


        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'fantasy_name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:255|unique:academic_entities',
            'foundation_date' => 'required|date',
            'status' => 'required|string|max:255',
            'cep' => 'required|string|max:255',
        ], ['cnpj.unique' => 'O CNPJ informado já está cadastrado.']
        );



        $validatedData['user_id'] = Auth::id();

        $academicEntity = AcademicEntity::create($validatedData);

        return response()->json($academicEntity, 201);
    }


     /**
     * @OA\Get(
     *     path="/api/academic-entities/{id}",
     *     tags={"Academic Entities"},
     *     summary="Obtém uma entidade acadêmica específica",
     *     operationId="getAcademicEntity",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da entidade acadêmica",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entidade encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicEntity")
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
     *         description="Entidade não encontrada"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(AcademicEntity $academicEntity)
    {
        if ($academicEntity->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        return response()->json($academicEntity, 200);
    }


        /**
     * @OA\Put(
     *     path="/api/academic-entities/{id}",
     *     tags={"Academic Entities"},
     *     summary="Atualiza uma entidade acadêmica",
     *     operationId="updateAcademicEntity",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da entidade acadêmica",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados atualizados da entidade acadêmica",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicEntity")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entidade atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicEntity")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, AcademicEntity $academicEntity)
    {
        if ($academicEntity->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validatedData = $request->validate([
            'type' => 'sometimes|string|max:255',
            'fantasy_name' => 'sometimes|string|max:255',
            'cnpj' => [
                'sometimes',
                'string',
                'max:255',
                'unique:academic_entities,cnpj,'.$academicEntity->id
            ],
            'foundation_date' => 'sometimes|date',
            'status' => 'sometimes|string|max:255',
            'cep' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        $academicEntity->update($validatedData);
        return response()->json($academicEntity, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy( Int $id )
    // {
    //     $academicEntity->delete();
    //     return response()->json(null, 204);
    // }

    /**
     * @OA\Delete(
     *     path="/api/academic-entities/{id}",
     *     tags={"Academic Entities"},
     *     summary="Remove uma entidade acadêmica",
     *     operationId="deleteAcademicEntity",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da entidade acadêmica",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Entidade removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entidade não encontrada"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(string $id) 
    {
        $academicEntity = AcademicEntity::find($id);

    if (is_null($academicEntity)) {
        return response()->json(['message' => 'Entidade acadêmica não encontrada.'], 404);
    }

    if ($academicEntity->user_id !== Auth::id()) {
        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }

    $academicEntity->delete();

    return response()->json(null, 204);
    }
}
