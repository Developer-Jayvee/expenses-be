<?php

use App\Enums\BillStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->applyStatusValues(collect(BillStatusEnum::cases())->map->value->all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('bills')->where('status', 'ongoing')->update(['status' => 'active']);

        $this->applyStatusValues(['active', 'inactive', 'completed']);
    }

    /**
     * Update the bills.status enum/check constraint to the given allowed values,
     * using the syntax each database driver actually supports.
     *
     * @param  string[]  $values
     */
    private function applyStatusValues(array $values): void
    {
        match (DB::getDriverName()) {
            'mysql', 'mariadb' => DB::statement(sprintf(
                'ALTER TABLE bills MODIFY status ENUM(%s) NOT NULL',
                collect($values)->map(fn (string $value) => "'{$value}'")->implode(',')
            )),
            'pgsql' => $this->applyPostgresCheckConstraint($values),
            default => Schema::table('bills', function (Blueprint $table) use ($values) {
                $table->enum('status', $values)->change();
            }),
        };
    }

    /**
     * @param  string[]  $values
     */
    private function applyPostgresCheckConstraint(array $values): void
    {
        $quoted = collect($values)->map(fn (string $value) => "'{$value}'")->implode(',');

        DB::statement('ALTER TABLE bills DROP CONSTRAINT IF EXISTS bills_status_check');
        DB::statement("ALTER TABLE bills ADD CONSTRAINT bills_status_check CHECK (status IN ({$quoted}))");
    }
};
