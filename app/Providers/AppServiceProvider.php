<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission; // Ajustez le namespace si votre modèle s'appelle autrement

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

     public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            // 1. Accès complet si l'utilisateur est Administrateur
            $roleName = is_object($user->role) ? ($user->role->name ?? '') : ($user->role ?? '');
            if (in_array(strtolower($roleName), ['admin', 'super_admin', 'administrateur'])) {
                return true;
            }

            // 2. Si le modèle User possède déjà une méthode hasPermission()
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }

            // 3. Si le rôle possède un tableau/collection 'permissions' (JSON ou relation)
            if (is_object($user->role) && isset($user->role->permissions)) {
                $permissions = $user->role->permissions;

                if (is_array($permissions)) {
                    return in_array($ability, $permissions);
                }

                if ($permissions instanceof \Illuminate\Support\Collection) {
                    return $permissions->contains($ability) || $permissions->contains('name', $ability);
                }
            }

            return null;
        });
    }
}
