<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Autorização já tratada pela rota com middleware('role:admin')
        $valves = Valve::orderBy('valve_number')->paginate(10);
        return view('admin.valves.index', compact('valves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.valves.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'valve_number' => [
                'required',
                'integer',
                'min:1',
                'max:5', // Conforme os requisitos, são 5 válvulas
                Rule::unique('valves', 'valve_number')
            ],
            'description' => 'nullable|string',
            'esp32_pin' => 'nullable|integer|min:0',
        ]);

        // current_state e last_activated_at são geridos pelo sistema/ESP32, não no CRUD básico.
        Valve::create($validatedData);

        return redirect()->route('admin.valves.index')->with('success', 'Válvula criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Valve $valve)
    {
        return view('admin.valves.show', compact('valve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Valve $valve)
    {
        return view('admin.valves.edit', compact('valve'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Valve $valve)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'valve_number' => [
                'required',
                'integer',
                'min:1',
                'max:5',
                Rule::unique('valves', 'valve_number')->ignore($valve->id)
            ],
            'description' => 'nullable|string',
            'esp32_pin' => 'nullable|integer|min:0',
        ]);

        $valve->update($validatedData);

        return redirect()->route('admin.valves.index')->with('success', 'Válvula atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Valve $valve)
    {
        // Considerar o que acontece com os operation_logs associados.
        // Por defeito, se houver foreign key constraint, não permitirá apagar.
        // Pode ser necessário tratar os logs ou usar onDelete('set null') na migração.
        try {
            $valve->delete();
            return redirect()->route('admin.valves.index')->with('success', 'Válvula eliminada com sucesso.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Se houver erro de integridade referencial (ex: logs associados)
            return redirect()->route('admin.valves.index')->with('error', 'Não foi possível eliminar a válvula. Verifique se existem logs associados.');
        }
    }
}
