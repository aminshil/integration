<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findAllSortedByNom($sortOrder = 'asc')
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.nom', $sortOrder)
        ->getQuery()
        ->getResult();
}

// Custom method to retrieve events sorted by date_debut
public function findAllSortedBy($sortBy, $sortOrder = 'asc')
{
    $qb = $this->createQueryBuilder('e');

    switch ($sortBy) {
        case 'id': // Adding sorting by ID
            $qb->orderBy('e.id', $sortOrder);
            break;
        case 'nom':
            $qb->orderBy('e.nom', $sortOrder);
            break;
        case 'date_debut':
            $qb->orderBy('e.date_debut', $sortOrder);
            break;
        case 'etat':
            $qb->orderBy('e.etat', $sortOrder);
            break;
            case 'Nbrparticipants':
            // Join the participationevenement collection
            $qb->leftJoin('e.participationevenement', 'p');
            // Group by the evenement to avoid duplicate rows
            $qb->groupBy('e.id');
            // Order by the count of participants
            $qb->orderBy('COUNT(p)', $sortOrder);
            break;
        case 'period':
            // Fetch all events from the repository
            $sortedEntities = $this->findAll();

            // Sort the entities by the calculated property 'period'
            usort($sortedEntities, function ($a, $b) use ($sortOrder) {
                $periodA = $a->calculatePeriodeTemp();
                $periodB = $b->calculatePeriodeTemp();

                if ($periodA == $periodB) {
                    return 0;
                }
                if ($sortOrder=='asc'){
                return ($periodA < $periodB) ? -1 : 1;}
                if ($sortOrder=='desc'){
                    return ($periodA > $periodB) ? -1 : 1;}
            });

            return $sortedEntities;
        case 'NumberOfComment':
            // Fetch all events from the repository
            $sortedEntities = $this->findAll();

            // Sort the entities by the calculated property 'NumberOfComment'
            usort($sortedEntities, function ($a, $b) use ($sortOrder) {
                $commentsA = $a->getNumberOfComments();
                $commentsB = $b->getNumberOfComments();

                if ($commentsA == $commentsB) {
                    return 0;
                }
                if ($sortOrder=='asc'){
                    return ($commentsA < $commentsB) ? -1 : 1;}
                if ($sortOrder=='desc'){
                    return ($commentsA > $commentsB) ? -1 : 1;}
            });

            return $sortedEntities;
        default:
            // Default to sorting by name if an invalid sort by parameter is provided
            $qb->orderBy('e.nom', $sortOrder);
    }

    return $qb->getQuery()->getResult();
}



}
