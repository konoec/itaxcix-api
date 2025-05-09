<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\Usuario;

class UsuarioRepository extends EntityRepository {

    /**
     * Busca un usuario por su alias.
     *
     * @param string $alias
     * @return Usuario|null
     */
    public function findByAlias(string $alias): ?Usuario {
        return $this->findOneBy(['alias' => $alias]);
    }

    public function existsByAlias(string $alias): bool {
        return $this->findByAlias($alias) !== null;
    }

    /**
     * Válida si el usuario existe, tiene estado activo y la contraseña coincide.
     *
     * @param string $alias
     * @param string $password Contraseña sin encriptar (para comparar)
     * @return Usuario|null Retorna el usuario si las credenciales son válidas, null en caso contrario
     */
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