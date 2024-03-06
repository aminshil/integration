<?php
namespace App\Service;

use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;

class EvenementStateService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateAllEvenementsState(): void
    {
        $evenements = $this->entityManager->getRepository(Evenement::class)->findAll();

        foreach ($evenements as $evenement) {
            $evenement->updateEtat();
        }

        $this->entityManager->flush();
    }
}