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
                $table->integer("coverage_count")->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('transaction','coverage_count')) {
            Schema::dropColumns('transactions',['coverage_count']);
        }
    }
};
