<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PermissionCategory;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SecuritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cargar Permisos
        $permissionsJson = File::get(database_path('data/permissions.json'));
        $permissions = json_decode($permissionsJson, true);

        foreach ($permissions as $permData) {
            // Buscar o crear la categoría
            $category = PermissionCategory::firstOrCreate([
                'name' => $permData['categoria']
            ]);

            // Crear o actualizar el permiso
            Permission::updateOrCreate(
                ['name' => $permData['name'], 'guard_name' => $permData['guard']],
                [
                    'description' => $permData['description'],
                    'permission_category_id' => $category->id
                ]
            );
        }

        // 2. Cargar Roles
        $rolesJson = File::get(database_path('data/roles.json'));
        $roles = json_decode($rolesJson, true);

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']]
            );

            // Si es Admin, le asignamos todos los permisos por defecto
            if ($role->name === 'Admin') {
                $allPermissions = Permission::all();
                $role->syncPermissions($allPermissions);
            }
        }
    }
}
