<?php

namespace App\Http\Requests\Admin;

use App\Support\SystemRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(SystemRoles::ADMIN) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('roles', 'name')],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists(Permission::class, 'name')->where('guard_name', 'web'),
            ],
        ];
    }
}
