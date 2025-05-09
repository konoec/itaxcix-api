<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\ContactoUsuario;

class ContactoUsuarioRepository extends EntityRepository {

    public function findByTypeAndValue(int $contactTypeId, string $contactValue): ?ContactoUsuario {
        return $this->createQueryBuilder('c')
            ->andWhere('c.tipo = :typeId')
            ->andWhere('c.valor = :valor')
            ->setParameter('typeId', $contactTypeId)
            ->setParameter('valor', $contactValue)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByValor(string $valor): bool {
        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.valor = :valor')
            ->setParameter('valor', $valor)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result > 0;
    }

    public function save(ContactoUsuario $contactoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($contactoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(ContactoUsuario $contactoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($contactoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}