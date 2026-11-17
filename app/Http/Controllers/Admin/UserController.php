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
                // Libellés lisibles en français pour chaque permission
        $permissionLabels = [
            'voir_dashboard'     => '📊 Tableau de bord général',
            'gerer_checkin'      => '🛎️ Réservation / Check-in',
            'voir_rack'          => '🗓️ Rack Hôtelier Graphique',
            'voir_iot'           => '📡 Suivi des Cartes RFID (IoT)',
            'voir_chambres'      => '🚪 État des Chambres',
            'gerer_menage'       => '🧹 Planification & Audit Ménage',
            'gerer_restauration' => '🍽️ Prise de Commande (Resto/Bar)',
            'gerer_services'     => '👔 Pressing & Lavage Auto',
            'gerer_activites'    => '🏊 Activités Annexes (Piscine/Spa/Navette)',
            'gerer_encours'      => '🧾 Encours & Factures',
            'voir_grand_livre'   => '💰 Grand Livre des recettes',
            'gerer_depenses'     => '💸 Dépenses & Petite Caisse',
            'voir_rapport_z'     => '🔐 Clôture Caisse (Rapport Z)',
            'gerer_billetterie'  => '🎟️ Émettre un Billet (Spectacles)',
            'configurer_salles'  => '⚙️ Configuration (Salles, Concerts, Utilisateurs)',
        ];


        return view('admin.users.index', compact('employees', 'roles', 'permissions', 'permissionLabels'));
    }

        public function store(Request $request)
    {
        $request->merge([
            'email' => trim(strtolower($request->input('email'))),
        ]);

        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ], [
            'name.required'     => 'Le nom complet de l\'employé est obligatoire.',
            'email.required'    => 'L\'adresse e-mail est obligatoire.',
            'email.email'       => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique'      => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit contenir au moins 6 caractères.',
            'role.required'     => 'Veuillez sélectionner un rôle pour cet utilisateur.',
            'role.exists'       => 'Le rôle choisi n\'existe pas.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', "Compte créé pour {$user->name} avec le rôle " . strtoupper($request->role) . ".");
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

        /**
     * Réinitialiser rapidement le mot de passe d'un employé
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.required'  => 'Veuillez saisir le nouveau mot de passe.',
            'new_password.min'       => 'Le mot de passe doit contenir au moins 6 caractères.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', "Le mot de passe de {$user->name} a été réinitialisé avec succès.");
    }

}
