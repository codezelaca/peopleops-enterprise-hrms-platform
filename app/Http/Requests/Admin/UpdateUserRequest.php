<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(SystemRoles::ADMIN) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $managedUser */
        $managedUser = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($managedUser)],
            'nic' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9\\-\\.\\/]+$/', Rule::unique('users', 'nic')->ignore($managedUser)],
            'phone' => ['nullable', 'string', 'max:40'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'password' => ['nullable', 'string', Password::defaults()],
            'roles' => ['array', 'max:5'],
            'roles.*' => [
                'string',
                Rule::exists(Role::class, 'name')->where('guard_name', 'web'),
                Rule::notIn([SystemRoles::ADMIN]),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nic.regex' => 'The NIC may only contain letters, numbers, dashes, dots, and slashes.',
            'roles.*.not_in' => 'The administrator role is protected and cannot be assigned.',
        ];
    }
}
