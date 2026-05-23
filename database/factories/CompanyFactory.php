<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->company(),
            'legal_name' => fake()->company().' Ltd',
            'registration_number' => fake()->bothify('REG-####'),
            'tax_id' => fake()->bothify('TAX-####'),
            'industry' => 'Professional Services',
            'company_size' => '11-50',
            'website' => 'https://example.com',
            'support_email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'timezone' => 'Asia/Colombo',
            'country' => 'LK',
            'city' => 'Colombo',
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'postal_code' => fake()->postcode(),
            'default_currency' => 'LKR',
            'fiscal_year_start_month' => 1,
            'work_week_starts_on' => 'monday',
            'setup_by_user_id' => User::factory(),
            'onboarding_completed_at' => now(),
        ];
    }
}
