<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Query;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface QueryHandler extends MessageHandlerInterface
{
}
