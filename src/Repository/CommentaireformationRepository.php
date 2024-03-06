<?php

namespace App\Repository;

use App\Entity\Commentaireformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaireformation>
 *
 * @method Commentaireformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaireformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaireformation[]    findAll()
 * @method Commentaireformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaireformation::class);
    }

//    /**
//     * @return Commentaireformation[] Returns an array of Commentaireformation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commentaireformation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
