<?php

declare(strict_types = 1);

namespace App\Infrastructure\Messenger\Event;

use App\Shared\Domain\Bus\Event\Event;
use App\Shared\Domain\Bus\Event\DomainEventPublisher;

class SymfonyDomainEventPublisher implements DomainEventPublisher
{
    private $events = [];

    public function publish(Event ...$events): void
    {
        $this->events = array_merge($this->events, array_values($events));
    }

    public function release(): array
    {
        $events       = $this->events;
        $this->events = [];

        return $events;
    }
}
