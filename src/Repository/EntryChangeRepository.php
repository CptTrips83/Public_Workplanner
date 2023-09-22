<?php

namespace App\Repository;

use App\Entity\WorkEntryChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkEntryChange>
 *
 * @method WorkEntryChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkEntryChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkEntryChange[]    findAll()
 * @method WorkEntryChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkEntryChange::class);
    }

//    /**
//     * @return WorkEntryChange[] Returns an array of WorkEntryChange objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorkEntryChange
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
