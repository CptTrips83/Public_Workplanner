<?php

namespace App\Repository;

use App\Entity\PauseKategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PauseKategorie>
 *
 * @method PauseKategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method PauseKategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method PauseKategorie[]    findAll()
 * @method PauseKategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseKategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PauseKategorie::class);
    }

    public function save(PauseKategorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PauseKategorie[] Returns an array of PauseKategorie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PauseKategorie
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
