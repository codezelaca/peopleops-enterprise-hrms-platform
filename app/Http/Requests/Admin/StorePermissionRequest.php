<?php

namespace App\Http\Requests\Admin;

use App\Support\SystemRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:[\\.\\-][a-z0-9]+)*$/', Rule::unique('permissions', 'name')],
        ];
    }
}
