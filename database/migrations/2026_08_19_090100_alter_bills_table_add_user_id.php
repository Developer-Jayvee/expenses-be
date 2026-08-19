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
        if (! Schema::hasColumn('bills', 'user_id')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bills', 'user_id')) {
            Schema::dropColumns('bills', ['user_id']);
        }
    }
};
