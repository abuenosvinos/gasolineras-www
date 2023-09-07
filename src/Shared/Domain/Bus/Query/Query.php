<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Query;

use App\Shared\Domain\Bus\Request;

class Query extends Request
{
    public function requestType(): string
    {
        return 'query';
    }
}
