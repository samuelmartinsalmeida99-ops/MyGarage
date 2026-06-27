<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    // List all vehicles
    public function index()
    {
        $vehicles = Vehicle::latest()->get();
        return Inertia::render("Vehicles/Index", ['vehicles' => $vehicles]);
    }

    // Store a new vehicle
    public function store(Request $request)
    {
        // 1. Validação dos dados (garante que os nomes batem certo com o Vue)
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer',
            'kilometers' => 'required|integer',
            'plate_number' => 'required|string|max:8',
            'iuc_paid' => 'boolean',
            'next_inspection_date' => 'nullable|date',
            'inspection_done' => 'boolean',
        ]);

        // 2. Criação do registo na BD injetando o ID do utilizador autenticado
        // Isto evita o erro de "undefined method User::vehicles()"
        Vehicle::create(array_merge($validated, [
            'user_id' => auth()->id()
        ]));

        // 3. Redirecionar de volta para atualizar a tabela no Inertia
        return redirect()->back();
    }

    // Update an existing vehicle
    public function update(Request $request, Vehicle $vehicle)
    {
        
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'kilometers' => 'required|integer|min:0', 
            'plate_number' => 'required|string|max:8|unique:vehicles,plate_number,' . $vehicle->id,
            'iuc_paid' => 'boolean',
            'next_inspection_date' => 'nullable|date',
            'inspection_done' => 'boolean',
        ]);

        $vehicle->update($validated);

        return redirect()->route('dashboard')->with('success', 'Veículo atualizado com sucesso.');
    }

    // Delete a vehicle
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Veículo apagado com sucesso.');
    }
}