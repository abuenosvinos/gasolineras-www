<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Command;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface CommandHandler extends MessageHandlerInterface
{
}
