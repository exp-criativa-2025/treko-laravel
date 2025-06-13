<?php

namespace App\Http\Controllers;

use App\Models\AcademicEntity;
use Illuminate\Http\Request;

class AcademicEntityController extends Controller
{
    public readonly AcademicEntity $academicEntity;

    public function __construct()
    {
        $this->academicEntity = new AcademicEntity();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->academicEntity->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'type' => 'required|string|max:255',
            'fantasy_name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:255|unique:academic_entities',
            'foundation_date' => 'required|date',
            'status' => 'required|string|max:255',
            'cep' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id'
        ]);

        $academicEntity = AcademicEntity::create($validated);

        return response()->json($academicEntity, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicEntity $academicEntity)
    {
        return response()->json($academicEntity);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicEntity $academicEntity)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'fantasy_name' => 'sometimes|string|max:255',
            'cnpj' => 'sometimes|string|max:255|unique:academic_entities,cnpj,'.$academicEntity->id,
            'foundation_date' => 'sometimes|date',
            'status' => 'sometimes|string|max:255',
            'cep' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        $academicEntity->update($validated);

        return response()->json($academicEntity);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicEntity $academicEntity)
    {
        $academicEntity->delete();
        return response()->json(null, 204);
    }
}
