<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\File;
use App\Shared\Domain\Criteria\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    use DoctrineRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function searchFilesWithTotalStations(Criteria $criteria)
    {
        $criteria = $this->transformCriteria($criteria);

        $q = $this->createQueryBuilder('f')
            ->leftJoin('f.stations', 's')
            ->addSelect('COUNT(f.id)')
            ->addGroupBy('f.id')
        ;
        $q->addCriteria($criteria);

        $paginator = new Paginator($q);

        return $paginator;
    }

    public function findActiveFile()
    {
        return $this->findOneBy(['active' => 1]);
    }

    public function createFile($name, $nameFile)
    {
        $file = new File();
        $file->setName($name);
        $file->setFile($nameFile);
        $file->setActive(false);

        $this->save($file);

        return $file;
    }

    public function deleteStationsFromFile(File $file)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('App\Domain\Entity\Station', 's')
            ->where('s.file = :id')
            ->setParameter('id', $file->getId());
        $qb->getQuery()->execute();
    }

    public function inactiveAllFiles()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update('App\Domain\Entity\File', 'f')
            ->set('f.active', 0);
        $qb->getQuery()->execute();
    }

    public function getLastFiles()
    {
        return $this->findBy([], ['id' => 'desc'], 3, 10);
    }

    public function remove(File $file)
    {
        $this->getEntityManager()->remove($file);
    }

    public function save(File $file)
    {
        $this->getEntityManager()->persist($file);
    }


    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
