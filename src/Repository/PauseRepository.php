<?php

namespace App\Repository;

use App\Entity\Pause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pause>
 *
 * @method Pause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pause[]    findAll()
 * @method Pause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pause::class);
    }

    public function save(Pause $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pause $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    public function findOneBySomeField($value): ?Pause
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
