<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'uuid',
    'name',
    'legal_name',
    'registration_number',
    'tax_id',
    'industry',
    'company_size',
    'website',
    'support_email',
    'phone',
    'timezone',
    'country',
    'city',
    'address_line_1',
    'address_line_2',
    'postal_code',
    'default_currency',
    'fiscal_year_start_month',
    'work_week_starts_on',
    'logo_disk',
    'logo_path',
    'logo_original_name',
    'setup_by_user_id',
    'onboarding_completed_at',
])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, LogsActivity;

    public function setupBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'setup_by_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'legal_name',
                'industry',
                'company_size',
                'timezone',
                'default_currency',
                'onboarding_completed_at',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fiscal_year_start_month' => 'integer',
            'onboarding_completed_at' => 'immutable_datetime',
        ];
    }
}
