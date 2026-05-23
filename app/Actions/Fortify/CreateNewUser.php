<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\SystemBootstrap;
use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        if (User::query()->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Public sign up is only available before the first administrator account is created.',
            ]);
        }

        try {
            return DB::transaction(function () use ($input): User {
                SystemBootstrap::query()->create([
                    'id' => 1,
                    'registered_by_email' => $input['email'],
                    'completed_at' => now(),
                ]);

                $user = User::query()->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => $input['password'],
                ]);

                $adminRole = Role::query()->firstOrCreate([
                    'name' => SystemRoles::ADMIN,
                    'guard_name' => 'web',
                ]);

                $user->assignRole($adminRole);

                activity()
                    ->causedBy($user)
                    ->performedOn($user)
                    ->event('created')
                    ->log('Initial administrator account created');

                return $user;
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'email' => 'The initial administrator has already been created. Please log in instead.',
            ]);
        }
    }
}
