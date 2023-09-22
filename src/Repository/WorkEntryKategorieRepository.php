<?php

namespace App\Repository;

use App\Entity\WorkEntryKategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkEntryKategorie>
 *
 * @method WorkEntryKategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkEntryKategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkEntryKategorie[]    findAll()
 * @method WorkEntryKategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkEntryKategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkEntryKategorie::class);
    }

//    /**
//     * @return WorkEntryKategorie[] Returns an array of WorkEntryKategorie objects
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

//    public function findOneBySomeField($value): ?WorkEntryKategorie
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
