<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('legal_name');
            $table->string('registration_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('industry');
            $table->string('company_size');
            $table->string('website')->nullable();
            $table->string('support_email');
            $table->string('phone')->nullable();
            $table->string('timezone');
            $table->string('country', 2);
            $table->string('city');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('default_currency', 3);
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1);
            $table->string('work_week_starts_on')->default('monday');
            $table->string('logo_disk')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_original_name')->nullable();
            $table->foreignId('setup_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('onboarding_completed_at')->nullable()->index();
            $table->timestamps();

            $table->index(['industry', 'company_size']);
            $table->index(['country', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
