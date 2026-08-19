<?php

namespace App\Contracts;

/**
 * Generates unique group codes used to link users into a shared household
 * budget group.
 *
 * Kept as a single-method contract so services depend on this abstraction
 * (Dependency Inversion) instead of the concrete generator.
 */
interface GroupCodeGeneratorInterface
{
    public function generate(): string;
}
