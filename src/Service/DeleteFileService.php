<?php


namespace App\Service;


use App\Entity\File;
use Psr\Container\ContainerInterface;

class DeleteFileService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preRemove(File $entity) {
        if ($entity->getActive()) {
            throw new \Exception('No se puede borrar el registro activo');
        }
    }
    public function postRemove(File $entity) {
        $nameFile = $entity->getFile();
        $pathFile = $this->container->getParameter('download_save_path') . '/' . $nameFile;
        if (file_exists($pathFile)) {
            unlink($pathFile);
        }
    }
}