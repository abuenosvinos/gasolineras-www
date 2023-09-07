<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Price|null find($id, $lockMode = null, $lockVersion = null)
 * @method Price|null findOneBy(array $criteria, array $orderBy = null)
 * @method Price[]    findAll()
 * @method Price[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    public function save(Price $price)
    {
        $this->getEntityManager()->persist($price);
    }

    /*
    public function findOneBySomeField($value): ?Station
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
