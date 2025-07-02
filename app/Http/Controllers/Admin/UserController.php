<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'email_verified_at' => now(), // Opcional: Admin cria utilizadores já verificados
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilizador criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'confirmed', Password::defaults()], // Senha é opcional na atualização
        ]);

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        // Evitar que um admin se auto-rebaixe de papel ou se apague (lógica adicional pode ser necessária)
        // if (auth()->id() === $user->id && $request->role !== 'admin') {
        // return back()->with('error', 'Não pode alterar o seu próprio papel para não-admin.');
        // }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Utilizador atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Adicionar lógica para impedir que o utilizador se auto-elimine
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Não pode eliminar a sua própria conta.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Utilizador eliminado com sucesso.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.users.index')->with('error', 'Não foi possível eliminar o utilizador.');
        }
    }
}
