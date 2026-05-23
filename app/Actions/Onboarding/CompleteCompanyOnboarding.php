<?php

namespace App\Actions\Onboarding;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompleteCompanyOnboarding
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $admin, array $data, ?UploadedFile $logo): Company
    {
        return DB::transaction(function () use ($admin, $data, $logo): Company {
            $company = Company::query()->create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'legal_name' => $data['legal_name'],
                'registration_number' => $data['registration_number'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'industry' => $data['industry'],
                'company_size' => $data['company_size'],
                'website' => $data['website'] ?? null,
                'support_email' => $data['support_email'],
                'phone' => $data['phone'] ?? null,
                'timezone' => $data['timezone'],
                'country' => Str::upper($data['country']),
                'city' => $data['city'],
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'default_currency' => Str::upper($data['default_currency']),
                'fiscal_year_start_month' => (int) $data['fiscal_year_start_month'],
                'work_week_starts_on' => $data['work_week_starts_on'],
                'setup_by_user_id' => $admin->id,
                'onboarding_completed_at' => now(),
            ]);

            if ($logo instanceof UploadedFile) {
                $disk = config('filesystems.default');
                $path = $logo->store("companies/{$company->uuid}/brand", ['disk' => $disk]);

                $company->forceFill([
                    'logo_disk' => $disk,
                    'logo_path' => $path,
                    'logo_original_name' => $logo->getClientOriginalName(),
                ])->save();
            }

            $admin->forceFill(['company_id' => $company->id])->save();
            $admin->setRelation('company', $company);

            activity()
                ->causedBy($admin)
                ->performedOn($company)
                ->event('completed')
                ->log('Company onboarding completed');

            return $company;
        });
    }
}
