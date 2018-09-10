<?php

namespace App\Repository;

use App\Entity\WeatherRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WeatherRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeatherRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeatherRecord[]    findAll()
 * @method WeatherRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherRecordRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WeatherRecord::class);
    }

//    /**
//     * @return WeatherRecord[] Returns an array of WeatherRecord objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WeatherRecord
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
