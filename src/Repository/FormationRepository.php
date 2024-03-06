<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{


//    /**
//     * @return Formation[] Returns an array of Formation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Formation::class);
        $this->paginator = $paginator;
    }

    public function findAllSortedByNomQuery($sortOrder = 'asc'): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.nom_formation', $sortOrder);
    }
    public function findAllSortedByDateQuery($sortOrder = 'asc'): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.date_formation', $sortOrder);
    }

    public function findByNomQuery($name): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.nom_formation LIKE :name')
            ->setParameter('name', '%' . $name . '%');
    }

    public function paginateQuery(QueryBuilder $query, $page = 1, $limit = 3)
    {
        return $this->paginator->paginate(
            $query->getQuery(),
            $page,
            $limit
        );
    }
    public function findAllSortedByIdQuery($sortOrder = 'asc'): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.id', $sortOrder);
    }
}
