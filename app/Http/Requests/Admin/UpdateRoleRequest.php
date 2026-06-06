<?php

namespace App\Http\Requests\Admin;

use App\Support\SystemRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Role $role */
        $role = $this->route('role');

        return $this->user()?->hasRole(SystemRoles::ADMIN) === true
            && $role->name !== SystemRoles::ADMIN;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('roles', 'name')->ignore($role),
                Rule::notIn([SystemRoles::ADMIN]),
            ],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists(Permission::class, 'name')->where('guard_name', 'web'),
            ],
        ];
    }
}
