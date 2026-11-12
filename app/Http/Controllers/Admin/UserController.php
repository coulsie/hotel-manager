<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Liste le personnel et alimente la modale
     */
    public function index()
    {
        $employees   = User::with('roles')->get();
        $roles       = Role::with('permissions')->get();
        $permissions = Permission::all();

        // Libellés lisibles en français pour chaque permission
        $permissionLabels = [
            'voir_dashboard'     => '📊 Tableau de bord général',
            'gerer_checkin'      => '🛎️ Réservation / Check-in',
            'voir_iot'           => '📡 Suivi des Cartes RFID (IoT)',
            'voir_chambres'      => '🚪 État des Chambres',
            'gerer_menage'       => '🧹 Planification Ménage',
            'gerer_restauration' => '🍽️ Prise de Commande (Resto/Bar)',
            'gerer_services'     => '👔 Pressing & Lavage Auto',
            'gerer_encours'      => '🧾 Encours & Factures',
            'voir_grand_livre'   => '💰 Grand Livre des recettes',
            'voir_rapport_z'     => '🔐 Clôture Caisse (Rapport Z)',
            'gerer_billetterie'  => '🎟️ Émettre un Billet (Spectacles)',
            'configurer_salles'  => '⚙️ Configuration (Salles, Concerts, Utilisateurs)',
        ];

        return view('admin.users.index', compact('employees', 'roles', 'permissions', 'permissionLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', "Compte créé pour {$user->name} avec le rôle {$request->role}.");
    }
    /**
     * Enregistrer un nouvel employé (POST)
     */

    /**
     * Mettre à jour un employé existant (PUT)
     */
   

     public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', "Habilitations mises à jour pour {$user->name}.");
    }

    // Sauvegarder les cases cochées / décochées d'un rôle
    public function updateRolePermissions(Request $request, Role $role)
    {
        $selectedPermissions = $request->input('permissions', []);
        $role->syncPermissions($selectedPermissions);

        // Réinitialisation du cache Spatie Permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Matrice des accès mise à jour pour le rôle « " . strtoupper($role->name) . " ».");
    }
}
