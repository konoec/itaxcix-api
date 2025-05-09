<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\ContactoUsuario;
use itaxcix\models\entities\usuario\Usuario;

class UsuarioRepository extends EntityRepository {
    public function findOneByContact(ContactoUsuario $contacto): ?Usuario {
        return $this->createQueryBuilder('u')
            ->join('u.contactos', 'c')
            ->where('c = :contacto')
            ->setParameter('contacto', $contacto)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAlias(string $alias): ?Usuario {
        return $this->findOneBy(['alias' => $alias]);
    }

    public function existsByAlias(string $alias): bool {
        return $this->findByAlias($alias) !== null;
    }

    public function validateCredentials(string $alias, string $password): ?Usuario {
        $usuario = $this->findByAlias($alias);

        if (!$usuario) {
            return null;
        }

        if ($usuario->getEstado()?->getNombre() !== 'Activo') {
            return null;
        }

        if (!password_verify($password, $usuario->getClave())) {
            return null;
        }

        return $usuario;
    }

    public function save(Usuario $usuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($usuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Usuario $usuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($usuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}