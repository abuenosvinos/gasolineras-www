<?php

namespace App\Command;

use App\Entity\File;
use App\Entity\Station;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Port\Excel\ExcelReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CleanHistoryCommand extends Command
{
    protected static $defaultName = 'app:clean-history';

    private $entityManager;
    private $params;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete the oldest files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $files = $this->entityManager->getRepository(File::class)->findBy([], ['id' => 'desc'], 3, 10);
        $io->block('Prepare to delete: ' . count($files));
        foreach ($files as $file) {
            $io->block('Removed: ' . $file->getName());
            $this->entityManager->remove($file);
        }
        $this->entityManager->flush();

        $io->success('Execution succeed');
    }

    private function formatFloat($value) {
        return floatval(str_replace(',','.', $value));
    }
}
