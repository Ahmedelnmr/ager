<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'cheque'])->default('cash');
            $table->date('payment_date');
            $table->string('transaction_ref')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['rent_schedule_id']);
            $table->index(['contract_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
