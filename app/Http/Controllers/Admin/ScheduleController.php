<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::paginate(10);
        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schedules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'day_of_week' => [
                'required',
                'integer',
                'min:0', // 0 para Domingo
                'max:6', // 6 para Sábado
            ],
            'start_time' => 'required|date_format:H:i', // ou H:i:s se precisar de segundos
            'per_valve_duration_minutes' => 'required|integer|min:1',
            'is_enabled' => 'sometimes|boolean',
        ]);

        // Se 'is_enabled' não for enviado, o default é false, a menos que a base de dados tenha outro default.
        // Para garantir que é true se o checkbox estiver marcado e não enviado, ou false se não.
        $validatedData['is_enabled'] = $request->has('is_enabled');

        Schedule::create($validatedData);

        return redirect()->route('admin.schedules.index')->with('success', 'Agendamento criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'day_of_week' => [
                'required',
                'integer',
                'min:0',
                'max:6',
            ],
            'start_time' => 'required|date_format:H:i',
            'per_valve_duration_minutes' => 'required|integer|min:1',
            'is_enabled' => 'sometimes|boolean',
        ]);

        $validatedData['is_enabled'] = $request->has('is_enabled');

        $schedule->update($validatedData);

        return redirect()->route('admin.schedules.index')->with('success', 'Agendamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();
            return redirect()->route('admin.schedules.index')->with('success', 'Agendamento eliminado com sucesso.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.schedules.index')->with('error', 'Não foi possível eliminar o agendamento.');
        }
    }
}
