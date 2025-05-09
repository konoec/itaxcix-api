<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\RolUsuario;
use itaxcix\models\entities\usuario\Usuario;

class RolUsuarioRepository extends EntityRepository {

    /**
     * Obtiene los roles activos de un usuario.
     *
     * @param Usuario $usuario
     * @return array<int, string> Nombres de los roles
     */
    public function findActiveRolesByUsuario(Usuario $usuario): array {
        $qb = $this->createQueryBuilder('ru');

        $roles = $qb
            ->select('r.nombre')
            ->innerJoin('ru.rol', 'r')
            ->where('ru.usuario = :usuario')
            ->andWhere('ru.activo = :activo')
            ->andWhere('r.activo = :activo')
            ->setParameter('usuario', $usuario)
            ->setParameter('activo', true)
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['nombre'], $roles);
    }

    public function save(RolUsuario $rolUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($rolUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(RolUsuario $rolUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($rolUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}