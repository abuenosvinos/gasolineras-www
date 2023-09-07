<?php

declare(strict_types = 1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\ValueObject\Collection;

final class Filters extends Collection
{
    protected function type(): string
    {
        return Filter::class;
    }

    public static function fromValues(array $values): self
    {
        return new self(array_map(self::filterBuilder(), $values));
    }

    public function add(Filter $filter): self
    {
        return new self(array_merge($this->items(), [$filter]));
    }

    public function filters(): array
    {
        return $this->items();
    }

    private static function filterBuilder(): callable
    {
        return function (array $values) {
            return Filter::fromValues($values);
        };
    }
}
