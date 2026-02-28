<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('base_rent', 12, 2);
            $table->enum('payment_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->unsignedTinyInteger('due_day')->nullable()->comment('Day of month payment is due');
            // Security deposit
            $table->decimal('security_deposit_amount', 12, 2)->default(0);
            $table->enum('deposit_policy', ['refundable', 'deduct_last_month', 'non_refundable', 'partial'])->default('refundable');
            // Annual increase
            $table->enum('annual_increase_type', ['none', 'percent', 'fixed'])->default('none');
            $table->decimal('annual_increase_value', 10, 2)->default(0);
            // Late penalty
            $table->enum('late_penalty_type', ['none', 'percent', 'fixed'])->default('none');
            $table->decimal('late_penalty_value', 10, 2)->default(0);
            // Other
            $table->text('early_termination_policy')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable()->comment('Uploaded contract PDF/image');
            $table->json('settings')->nullable()->comment('Contract-level overrides of building settings');
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
