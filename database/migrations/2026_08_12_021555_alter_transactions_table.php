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
        if(Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->text("notes")->nullable();
                $table->date("transaction_date");
                $table->double('change')->default(0);
                if(Schema::hasColumn('transactions','payment_type')) {
                    $table->renameColumn("payment_type","payment_mode");
                }
            });
        } 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions' , function(Blueprint $table) {
            $table->renameColumn("payment_mode","payment_type");
        });
        Schema::dropColumns('transactions',['notes','transaction_date']);
    }
};
