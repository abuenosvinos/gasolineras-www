<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Bus\Event;

use App\Shared\Domain\Bus\Request;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use RuntimeException;

abstract class Event extends Request
{
    private $data;
    private $occurredOn;

    public function __construct(
        array $data = [],
        Uuid $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($eventId);

        $this->data        = $data;
        $this->occurredOn  = $occurredOn ?: (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }

    public function requestType(): string
    {
        return 'event';
    }

    public function data(): array
    {
        return $this->data;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }

    public function __call($method, $args)
    {
        $attributeName = $method;
        if (0 === strpos($method, 'is')) {
            $attributeName = lcfirst(substr($method, 2));
        }

        if (0 === strpos($method, 'has')) {
            $attributeName = lcfirst(substr($method, 3));
        }

        if (isset($this->data[$attributeName])) {
            return $this->data[$attributeName];
        }

        throw new RuntimeException(sprintf('The method "%s" does not exist.', $method));
    }
}
