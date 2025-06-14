<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{

    public readonly Campaign $campaign;

    public function __construct()
    {
        $this->campaign = new Campaign();
    }

     /**
     * @OA\Get(
     *     path="/api/campaigns",
     *     tags={"Campaigns"},
     *     summary="Lista todas as campanhas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de campanhas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Campaign")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $campaigns = $this->campaign->whereHas('academicEntity', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        return response()->json($campaigns, 200);
    }

    
    /**
     * @OA\Post(
     *     path="/api/campaigns",
     *     tags={"Campaigns"},
     *     summary="Cria uma nova campanha",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Campanha de Doação"),
     *             @OA\Property(property="goal", type="string", example="Arrecadar recursos para pesquisa"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="academic_entity_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Campanha criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Campaign")
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_entity_id' => 'required|exists:academic_entities,id'
        ]);

        // Verifica se a entidade acadêmica pertence ao usuário
        $academicEntity = AcademicEntity::find($validatedData['academic_entity_id']);
        if ($academicEntity->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $campaign = Campaign::create($validatedData);
        return response()->json($campaign, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        //
    }

        /**
     * @OA\Delete(
     *     path="/api/campaigns/{id}",
     *     tags={"Campaigns"},
     *     summary="Remove uma campanha",
     *     operationId="deleteCampaign",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da campanha",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Campanha removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campanha não encontrada"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Campaign $campaign)
    {
        if ($campaign->academicEntity->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $campaign->delete();
        return response()->json(null, 204);
    }
}
