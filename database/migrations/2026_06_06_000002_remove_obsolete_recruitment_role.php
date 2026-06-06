<?php

use App\Support\SystemRoles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $obsoleteRoleName = implode('', ['rec', 'ruit', 'er']);

        $obsoleteRole = Role::query()
            ->where('name', $obsoleteRoleName)
            ->where('guard_name', 'web')
            ->first();

        if (! $obsoleteRole) {
            return;
        }

        $hrManager = Role::query()->firstOrCreate([
            'name' => SystemRoles::HR_MANAGER,
            'guard_name' => 'web',
        ]);

        DB::table('model_has_roles')
            ->where('role_id', $obsoleteRole->id)
            ->orderBy('model_id')
            ->get(['model_type', 'model_id'])
            ->each(function (object $assignment) use ($hrManager): void {
                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $hrManager->id,
                    'model_type' => $assignment->model_type,
                    'model_id' => $assignment->model_id,
                ]);
            });

        DB::table('model_has_roles')
            ->where('role_id', $obsoleteRole->id)
            ->delete();

        $obsoleteRole->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
