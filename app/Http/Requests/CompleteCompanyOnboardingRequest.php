<?php

namespace App\Http\Requests;

use App\Support\SystemRoles;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteCompanyOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(SystemRoles::ADMIN) === true
            && $this->user()?->company === null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'legal_name' => ['required', 'string', 'max:160'],
            'registration_number' => ['nullable', 'string', 'max:80'],
            'tax_id' => ['nullable', 'string', 'max:80'],
            'industry' => ['required', 'string', 'max:80'],
            'company_size' => ['required', Rule::in(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'])],
            'website' => ['nullable', 'url', 'max:180'],
            'support_email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'timezone' => ['required', 'timezone', 'max:80'],
            'country' => ['required', 'string', 'size:2'],
            'city' => ['required', 'string', 'max:100'],
            'address_line_1' => ['required', 'string', 'max:180'],
            'address_line_2' => ['nullable', 'string', 'max:180'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'default_currency' => ['required', 'string', 'size:3'],
            'fiscal_year_start_month' => ['required', 'integer', 'between:1,12'],
            'work_week_starts_on' => ['required', Rule::in(['monday', 'sunday', 'saturday'])],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'support_email.email' => 'Enter a real company email address that can receive HR and system notices.',
            'logo.max' => 'The logo must be 2 MB or smaller.',
        ];
    }
}
