<?php

namespace App\Services;

use App\Abstracts\ResponseStatus;
use App\Traits\Response\ComplexResponse;
use App\Traits\Response\SimplifyResponse;

class RequestResponseService extends ResponseStatus
{
    use SimplifyResponse, ComplexResponse;
}
