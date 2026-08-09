<?php

use App\Enums\BillCategoryEnum;
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
        if(Schema::hasTable('bills')) {
           Schema::table('bills', function (Blueprint $table) {
                $table->enum("category",BillCategoryEnum::cases())->nullable();
                $table->boolean("is_autopay")->default(false);
                $table->enum("default_payment",PaymentTypesEnum::cases())->nullable();
                $table->string("description")->nullable();
                $table->enum("frequency",['monthly','yearly','once'])->nullable();
                $table->integer("day_of_month")->nullable();
            }); 
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
