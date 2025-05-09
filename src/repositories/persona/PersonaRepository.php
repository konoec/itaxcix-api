<?php

namespace itaxcix\repositories\persona;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\persona\Persona;

class PersonaRepository extends EntityRepository {

    /**
     * Verifica si existe una persona por su documento.
     *
     * @param string $documento
     * @return bool
     */
    public function existsByDocumento(string $documento): bool {
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.documento = :documento')
            ->setParameter('documento', $documento)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result > 0;
    }

    public function save(Persona $persona, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($persona);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Persona $persona, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($persona);

        if ($flush) {
            $entityManager->flush();
        }
    }
}