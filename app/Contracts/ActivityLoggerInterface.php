<?php

namespace App\Contracts;

use App\Enums\ActivityTypeEnum;
use App\Models\BillsModel;

/**
 * Writes activity log entries for a bill.
 *
 * Kept as a single-method contract so services depend on this abstraction
 * (Dependency Inversion) instead of the concrete logger, and stay free to
 * log without knowing how/where entries are persisted.
 */
interface ActivityLoggerInterface
{
    public function log(BillsModel $bill, ActivityTypeEnum $type, string $description): void;
}
