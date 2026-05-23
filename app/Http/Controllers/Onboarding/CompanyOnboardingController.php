<?php

namespace App\Http\Controllers\Onboarding;

use App\Actions\Onboarding\CompleteCompanyOnboarding;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteCompanyOnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CompanyOnboardingController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        if (request()->user()->company?->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('onboarding/CompanySetup', [
            'companySizeOptions' => [
                ['label' => '1-10 employees', 'value' => '1-10'],
                ['label' => '11-50 employees', 'value' => '11-50'],
                ['label' => '51-200 employees', 'value' => '51-200'],
                ['label' => '201-500 employees', 'value' => '201-500'],
                ['label' => '501-1,000 employees', 'value' => '501-1000'],
                ['label' => '1,000+ employees', 'value' => '1000+'],
            ],
            'workWeekOptions' => [
                ['label' => 'Monday', 'value' => 'monday'],
                ['label' => 'Sunday', 'value' => 'sunday'],
                ['label' => 'Saturday', 'value' => 'saturday'],
            ],
            'monthOptions' => collect(range(1, 12))->map(fn (int $month): array => [
                'label' => now()->startOfYear()->month($month)->format('F'),
                'value' => (string) $month,
            ])->all(),
        ]);
    }

    public function store(
        CompleteCompanyOnboardingRequest $request,
        CompleteCompanyOnboarding $completeCompanyOnboarding,
    ): RedirectResponse {
        $completeCompanyOnboarding->handle(
            $request->user(),
            $request->validated(),
            $request->file('logo'),
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Company setup completed. Your admin workspace is ready.');
    }
}
