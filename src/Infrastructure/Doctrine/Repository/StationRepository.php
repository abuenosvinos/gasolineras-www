<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    /**
     * @return Station[] Returns an array of Station objects
     */
    public function search($lat, $lng, $radius, $gas = null)
    {
        // Información obtenida de:
        // https://intelligentbee.com/2017/09/14/get-nearby-locations-mysql-database/
        // https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#findnearsql

        $q = $this->createQueryBuilder('s')
            //->andWhere('( 3959 * acos( cos( radians(37) ) * cos( radians( :lat ) ) * cos( radians( :lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( :lat ) ) ) ) AS distance')
            //->select('s.lat, s.lng, ((ACOS(SIN(:lat * PI() / 180) * SIN(s.lat * PI() / 180) + COS(:lat * PI() / 180) * COS(s.lat * PI() / 180) * COS((:lng - s.lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) as distance')
            ->leftJoin('s.file', 'f')
            ->select('
            s.id, s.lat, s.lng, s.municipality as city, s.address, s.label, s.schedule, s.price_gasoline_95 as gas95, s.price_gasoline_98 as gas98, s.price_diesel_a as diesel,	
            ( 6371 * acos( cos( radians(:lat) ) * cos( radians( s.lat ) ) * cos( radians( s.lng ) - radians(:lng) ) + sin( radians(:lat) ) * sin( radians( s.lat ) ) ) ) as HIDDEN distance')
            ->andWhere('f.active = 1')
            ->having('distance < :radius')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->setParameter('radius', ($radius / 1000))
            ->orderBy('distance')
        ;

        if (isset($gas)) {
            switch ($gas) {
                case 'gas95':
                    $q->andWhere('s.price_gasoline_95 > 0');
                    break;
                case 'gas98':
                    $q->andWhere('s.price_gasoline_98 > 0');
                    break;
                case 'diesel':
                    $q->andWhere('s.price_diesel_a > 0');
                    break;
            }
        }

        return $q->getQuery()->getResult();
    }

    public function findByLatLng($lat, $lng): ?Station
    {
        $q = $this->createQueryBuilder('s')
            ->andWhere('s.lat = :lat')
            ->andWhere('s.lng = :lng')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
        ;

        return $q->getQuery()->getOneOrNullResult();
    }

    public function save(Station $station)
    {
        $this->getEntityManager()->persist($station);
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
