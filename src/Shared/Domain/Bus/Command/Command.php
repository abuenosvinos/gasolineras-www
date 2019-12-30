<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Command;

use App\Shared\Domain\Bus\Request;

abstract class Command extends Request
{
    public function requestType(): string
    {
        return 'command';
    }
}
