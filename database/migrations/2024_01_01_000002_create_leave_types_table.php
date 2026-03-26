<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_days_per_year')->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('requires_documentation')->default(false);
            $table->boolean('paid')->default(true);
            $table->boolean('carry_over_allowed')->default(false);
            $table->integer('max_carry_over_days')->default(0);
            $table->string('accrual_frequency')->default('monthly'); // monthly, quarterly, annually, one_time, as_needed
            $table->boolean('probation_restriction')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
