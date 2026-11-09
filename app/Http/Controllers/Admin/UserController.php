<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Liste le personnel et alimente la modale
     */
    public function index()
    {
        // On récupère les utilisateurs avec leurs rôles associés
        $employees = User::with('roles')->orderBy('name', 'asc')->get();
        $roles = Role::all();

        return view('admin.users.index', compact('employees', 'roles'));
    }

    /**
     * Enregistrer un nouvel employé (POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        // 1. Création du compte
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hachage sécurisé
        ]);

        // 2. Attribution du rôle Spatie
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', "Le compte de l'employé {$user->name} a été créé avec succès.");
    }

    /**
     * Mettre à jour un employé existant (PUT)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Mise à jour du mot de passe uniquement s'il est saisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Synchronisation du rôle (Spatie écrase l'ancien rôle automatiquement)
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', "Les habilitations de {$user->name} ont été mises à jour.");
    }
}
