<?php

namespace Database\Seeders;

use App\Support\SystemRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = collect([
            'admin.dashboard.view',
            'company.profile.manage',
            'users.manage',
            'roles.manage',
            'employees.view',
            'recruitment.view',
            'payroll.view',
            'documents.view',
            'audit.view',
        ])->map(fn (string $permission): Permission => Permission::query()->firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web',
        ]));

        $admin = Role::query()->firstOrCreate([
            'name' => SystemRoles::ADMIN,
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions($permissions);

        foreach ([
            SystemRoles::HR_MANAGER,
            SystemRoles::RECRUITER,
            SystemRoles::MANAGER,
            SystemRoles::FINANCE,
            SystemRoles::EMPLOYEE,
        ] as $role) {
            Role::query()->firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}
