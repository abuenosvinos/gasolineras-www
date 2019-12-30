<?php

declare (strict_types = 1);

namespace App\Shared\Domain\Bus;

use App\Shared\Domain\ValueObject\Uuid;

abstract class Request
{
    private $requestId;

    public function __construct(Uuid $requestId = null)
    {
        $this->requestId = $requestId ?: Uuid::random();
    }

    public function requestId(): Uuid
    {
        return $this->requestId;
    }

    abstract public function requestType(): string;
}
