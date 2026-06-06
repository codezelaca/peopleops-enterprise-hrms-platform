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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nic')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->after('nic');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('status')->default('active')->index()->after('company_id');
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropColumn([
                'nic',
                'phone',
                'job_title',
                'status',
                'deleted_at',
            ]);
        });
    }
};
