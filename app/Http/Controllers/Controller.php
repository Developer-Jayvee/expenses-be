<?php

namespace App\Http\Controllers;

use App\Traits\ErrorMessageTrait;
use App\Traits\SuccessMessageTrait;

abstract class Controller
{
    use SuccessMessageTrait, ErrorMessageTrait;
}
