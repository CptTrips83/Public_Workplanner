<?php

namespace App\Repository;

use App\Entity\WorkEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkEntry>
 *
 * @method WorkEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkEntry[]    findAll()
 * @method WorkEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkEntry::class);
    }

    public function save(WorkEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WorkEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return WorkEntry[] Returns an array of WorkEntry objects
     */
    public function findByDateTimeRange($userId, $startDatum, $endDatum, $aktiv = true): array
    {
        return $this->createQueryBuilder('w')            
            ->andWhere('w.startDatum BETWEEN :startDatum AND :endDatum')
            ->andWhere('w.user = :userId')
            ->andWhere('w.aktiv = :aktiv')
            ->setParameters(array('startDatum' => $startDatum, 'endDatum' => $endDatum, 'userId' => $userId, 'aktiv' => $aktiv))
            ->orderBy('w.startDatum', 'DESC')
            ->setMaxResults(365)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?WorkEntry
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
