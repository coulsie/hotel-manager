<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionServiceProvider;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer le cache des droits pour éviter les conflits
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Liste des habilitations fines de l'hôtel
        $permissions = [
            'gerer_chambres',
            'gerer_menu',
            'consulter_iot_logs',
            'emettre_billets',
            'configurer_salles',
            'voir_comptabilite'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Création et attribution des privilèges aux Rôles

        // Directeur / Administrateur Général (A tous les droits)
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        // Réceptionniste / Agent d'accueil au comptoir
        $receptionRole = Role::findOrCreate('receptionniste');
        $receptionRole->givePermissionTo(['emettre_billets', 'consulter_iot_logs']);

        // Service Restauration (Maître d'hôtel / Cuisine)
        $restoRole = Role::findOrCreate('restauration');
        $restoRole->givePermissionTo(['gerer_menu']);
    }
}
