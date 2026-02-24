<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['index', 'create', 'store', 'destroy']);
        $this->middleware('role:admin')->except(['show', 'edit', 'update']);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,usuario',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        // Solo puede ver su propio perfil o si es admin
        if (auth()->user()->id !== $user->id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para ver este perfil.');
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Solo puede editar su propio perfil o si es admin
        if (auth()->user()->id !== $user->id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para editar este usuario.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Solo puede actualizar su propio perfil o si es admin
        if (auth()->user()->id !== $user->id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para actualizar este usuario.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => auth()->user()->isAdmin() ? 'required|in:admin,usuario' : 'sometimes|in:admin,usuario',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Solo admin puede cambiar roles
        if (auth()->user()->isAdmin() && $request->has('role')) {
            $user->update(['role' => $request->role]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // No puede eliminarse a sí mismo
        if (auth()->user()->id === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
