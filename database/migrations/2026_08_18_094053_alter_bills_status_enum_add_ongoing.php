<?php

use App\Enums\BillStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values = collect(BillStatusEnum::cases())
            ->map(fn (BillStatusEnum $status) => "'{$status->value}'")
            ->implode(',');

        DB::statement("ALTER TABLE bills MODIFY status ENUM($values) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE bills SET status = 'active' WHERE status = 'ongoing'");
        DB::statement("ALTER TABLE bills MODIFY status ENUM('active','inactive','completed') NOT NULL");
    }
};
