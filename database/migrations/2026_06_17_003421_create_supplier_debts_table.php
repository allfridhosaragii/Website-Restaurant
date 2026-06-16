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
        Schema::create('supplier_debts', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->string('receipt_image')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_debts');
    }
};
