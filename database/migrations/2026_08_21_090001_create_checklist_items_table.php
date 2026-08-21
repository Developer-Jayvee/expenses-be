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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_group_id')->constrained('checklist_groups')->cascadeOnDelete();
            $table->string('item_name');
            $table->decimal('estimated_price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->index(['checklist_group_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
