<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function paginationQuery(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->getQuery();
    }



    public function findAllOrderedBy($sortField, $sortOrder): array
    {
        $qb = $this->createQueryBuilder('user')
            ->orderBy('user.' . $sortField, $sortOrder);

        return $qb->getQuery()->getResult();
    }
    public function findAllOrderedByRole($sortOrder = 'asc')
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.roles', $sortOrder)
            ->getQuery();

        return $qb->getResult();
    }

    // UserRepository.php

    public function findByRole(string $selectedRole, string $sortOrder): array
    {
        $users = $this->findAll(); // Fetch all users

        // Filter users based on the selected role
        $filteredUsers = array_filter($users, function ($user) use ($selectedRole) {
            return in_array($selectedRole, $user->getRoles());
        });

        // Sort the filtered users based on the desired field (e.g., 'nom')
        usort($filteredUsers, function ($a, $b) use ($sortOrder) {
            // Change 'nom' to the actual field you want to sort by
            return ($sortOrder === 'asc') ? strcmp($a->getNom(), $b->getNom()) : strcmp($b->getNom(), $a->getNom());
        });

        return $filteredUsers;
    }

    public function findBySearchQuery($searchQuery, $sortField = 'id', $sortOrder = 'asc')
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.nom LIKE :searchQuery OR u.email LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $searchQuery . '%')
            ->orderBy('u.' . $sortField, $sortOrder);

        return $qb->getQuery()->getResult();
    }
}
