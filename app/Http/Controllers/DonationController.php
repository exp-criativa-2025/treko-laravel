<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Donations",
 *     description="Operations about donations"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Donation",
 *     required={"donated", "date", "user_id", "campaign_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="donated", type="number", format="float", example=50.00),
 *     @OA\Property(property="date", type="string", format="date-time"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="campaign_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class DonationController extends Controller
{
    public readonly Donation $donation;

    public function __construct()
    {
        $this->donation = new Donation();
    }

    /**
     * @OA\Get(
     *     path="/api/donations",
     *     tags={"Donations"},
     *     summary="Lista todas as doações do usuário autenticado",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de doações",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Donation")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $donations = $this->donation->where('user_id', Auth::id())->get();
        return response()->json($donations, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/donations",
     *     tags={"Donations"},
     *     summary="Cria uma nova doação",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="donated", type="number", format="float", example=100.50),
     *             @OA\Property(property="date", type="string", format="date-time", example="2023-01-01 12:00:00"),
     *             @OA\Property(property="campaign_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Doação criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Donation")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O valor deve ser positivo."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'donated' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'campaign_id' => 'required|exists:campaigns,id'
        ], [
            'donated.min' => 'O valor deve ser positivo.',
            'campaign_id.exists' => 'A campanha selecionada não existe.'
        ]);

        $validatedData['user_id'] = Auth::id();
        $donation = Donation::create($validatedData);

        return response()->json($donation, 201);
    }


        /**
     * @OA\Get(
     *     path="/api/donations/{id}",
     *     tags={"Donations"},
     *     summary="Obtém uma doação específica",
     *     operationId="getDonation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da doação",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doação encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Donation")
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
     *         description="Doação não encontrada"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
        public function show(Donation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        return response()->json($donation, 200);
    }

     /**
     * @OA\Put(
     *     path="/api/donations/{id}",
     *     tags={"Donations"},
     *     summary="Atualiza uma doação",
     *     operationId="updateDonation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da doação",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="donated", type="number", format="float", example=150.75),
     *             @OA\Property(property="date", type="string", format="date-time", example="2023-01-02 15:30:00"),
     *             @OA\Property(property="campaign_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doação atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Donation")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado"
     *     ),
     * *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, Donation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validatedData = $request->validate([
            'donated' => 'sometimes|numeric|min:0.01',
            'date' => 'sometimes|date',
            'campaign_id' => 'sometimes|exists:campaigns,id'
        ], [
            'donated.min' => 'O valor deve ser positivo.',
            'campaign_id.exists' => 'A campanha selecionada não existe.'
        ]);

        $donation->update($validatedData);
        return response()->json($donation, 200);
    }

    
    /**
     * @OA\Delete(
     *     path="/api/donations/{id}",
     *     tags={"Donations"},
     *     summary="Remove uma doação",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da doação",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Doação removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doação não encontrada"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
     public function destroy(string $id)
    {
        $donation = Donation::find($id);

        if (is_null($donation)) {
            return response()->json(['message' => 'Doação não encontrada.'], 404);
        }

        if ($donation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $donation->delete();
        return response()->json(null, 204);
    }
}