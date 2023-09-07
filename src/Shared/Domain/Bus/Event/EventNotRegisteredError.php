<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Event;

final class EventNotRegisteredError extends \DomainException
{
    private $event;

    public function __construct(Event $event)
    {
        $this->event = $event;

        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'event_bus_not_registered_error';
    }

    protected function errorMessage(): string
    {
        return sprintf('The event <%s> has not been registered', get_class($this->event));
    }
}
