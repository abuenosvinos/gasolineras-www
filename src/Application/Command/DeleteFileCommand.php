<?php


namespace App\Application\Command;


use App\Shared\Domain\Bus\Command\Command;

class DeleteFileCommand extends Command
{
    private $id;

    public function __construct(string $id)
    {
        $this->id      = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}