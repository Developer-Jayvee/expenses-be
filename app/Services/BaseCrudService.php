<?php

namespace App\Services;

use App\Traits\ErrorMessageTrait;
use App\Traits\SuccessMessageTrait;

class BaseCrudService
{
    use SuccessMessageTrait, ErrorMessageTrait;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}
