<?php

declare(strict_types = 1);

namespace App\Framework\Controller;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Bus\Query\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    private $queryBus;
    private $commandBus;

    public function __construct(
        QueryBus $queryBus,
        CommandBus $commandBus
    ) {
        $this->queryBus         = $queryBus;
        $this->commandBus       = $commandBus;
    }

    protected function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    protected function ask(Query $query)//: ?Response
    {
        return $this->queryBus->ask($query);
    }
}
