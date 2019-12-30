<?php

declare(strict_types = 1);

namespace App\Infrastructure\Messenger\Event;

use App\Shared\Domain\Bus\Event\Event;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Shared\Domain\Bus\Event\EventNotRegisteredError;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class SymfonyEventBus implements EventBus
{
    private $bus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->bus = $eventBus;
    }

    public function notify(Event $event): void
    {
        try {
            $this->bus->dispatch( (new Envelope($event))
                ->with(new DispatchAfterCurrentBusStamp()));
        } catch (NoHandlerForMessageException $unused) {
            throw new EventNotRegisteredError($event);
        }
    }
}
