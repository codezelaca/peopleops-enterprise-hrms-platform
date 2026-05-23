<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user()->loadMissing('company');
        $company = $user->company;

        return Inertia::render('Dashboard', [
            'company' => [
                'name' => $company->name,
                'legalName' => $company->legal_name,
                'industry' => $company->industry,
                'companySize' => $company->company_size,
                'location' => trim($company->city.', '.$company->country),
                'timezone' => $company->timezone,
                'currency' => $company->default_currency,
                'logoUrl' => $company->logo_path ? route('company.logo.show') : null,
            ],
            'metrics' => [
                [
                    'label' => 'Active users',
                    'value' => User::query()->whereBelongsTo($company)->count(),
                    'description' => 'Users attached to this company workspace',
                ],
                [
                    'label' => 'Admin users',
                    'value' => User::query()->whereBelongsTo($company)->role('admin')->count(),
                    'description' => 'Protected system administration access',
                ],
                [
                    'label' => 'Workspace status',
                    'value' => $company->onboarding_completed_at ? 'Ready' : 'Setup',
                    'description' => 'Company onboarding completion state',
                ],
            ],
            'setupChecklist' => [
                ['label' => 'Company profile', 'complete' => true],
                ['label' => 'Admin account secured', 'complete' => $user->hasVerifiedEmail()],
                ['label' => 'Invite HR team', 'complete' => false],
                ['label' => 'Configure departments', 'complete' => false],
                ['label' => 'Connect R2 document storage', 'complete' => config('filesystems.default') === 's3'],
            ],
        ]);
    }
}
