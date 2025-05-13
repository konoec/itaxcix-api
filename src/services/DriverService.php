<?php

namespace itaxcix\services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use itaxcix\models\entities\usuario\PerfilConductor;

class DriverService {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    private function getPerfilConductorRepository(): EntityRepository {
        return $this->entityManager->getRepository(PerfilConductor::class);
    }

    public function activateAvailability(int $userId): void {
        $perfil = $this->getPerfilConductorRepository()->findOneBy(['usuario' => $userId]);

        if (!$perfil) {
            throw new Exception('Conductor no encontrado', 400);
        }

        $perfil->setDisponible(true);
        $this->getPerfilConductorRepository()->save($perfil);
    }

    public function deactivateAvailability(int $userId): void {
        $perfil = $this->getPerfilConductorRepository()->findOneBy(['usuario' => $userId]);

        if (!$perfil) {
            throw new Exception('Conductor no encontrado', 400);
        }

        $perfil->setDisponible(false);
        $this->getPerfilConductorRepository()->save($perfil);
    }
}