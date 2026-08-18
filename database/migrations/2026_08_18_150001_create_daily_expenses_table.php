<?php

use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
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
        Schema::create('daily_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_budget_id')->constrained('daily_budgets')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', DailyExpenseTypeEnum::cases());
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', PaymentTypesEnum::cases());
            $table->timestamps();

            $table->index(['daily_budget_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_expenses');
    }
};
