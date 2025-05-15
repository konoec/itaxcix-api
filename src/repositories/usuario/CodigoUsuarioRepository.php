<?php

namespace itaxcix\repositories\usuario;

use DateTime;
use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\CodigoUsuario;
use itaxcix\models\entities\usuario\ContactoUsuario;
use itaxcix\models\entities\usuario\TipoCodigoUsuario;
use itaxcix\models\entities\usuario\Usuario;

class CodigoUsuarioRepository extends EntityRepository {
    /**
     * Genera un nuevo código de recuperación.
     *
     * @param Usuario $usuario
     * @param ContactoUsuario $contacto
     * @param TipoCodigoUsuario $tipoCodigo
     * @return CodigoUsuario
     */
    public function generateRecoveryCode(
        Usuario $usuario,
        ContactoUsuario $contacto,
        TipoCodigoUsuario $tipoCodigo
    ): CodigoUsuario {
        $entityManager = $this->getEntityManager();

        $codigo = new CodigoUsuario();
        $codigo->setUsuario($usuario);
        $codigo->setContacto($contacto);
        $codigo->setTipo($tipoCodigo);
        $codigo->setCodigo($this->generateRandomCode());
        $codigo->setFechaExpiracion((new DateTime())->modify('+5 minutes'));

        $entityManager->persist($codigo);
        $entityManager->flush();

        return $codigo;
    }

    /**
     * Valida si un código es válido y no usado.
     *
     * @param string $code
     * @param ContactoUsuario $contacto
     * @param int $tipoCodigoId
     * @return CodigoUsuario|null
     */
    public function findValidCode(string $code, ContactoUsuario $contacto, int $tipoCodigoId): ?CodigoUsuario {
        return $this->createQueryBuilder('c')
            ->andWhere('c.codigo = :code')
            ->andWhere('c.contacto = :contacto')
            ->andWhere('c.tipo = :tipoCodigo')
            ->andWhere('c.usado = false')
            ->andWhere('c.fechaExpiracion > :now')
            ->setParameter('code', $code)
            ->setParameter('contacto', $contacto)
            ->setParameter('tipoCodigo', $tipoCodigoId)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Marca el código como usado.
     *
     * @param CodigoUsuario $codigo
     */
    public function markAsUsed(CodigoUsuario $codigo): void {
        $entityManager = $this->getEntityManager();

        $codigo->setUsado(true);
        $codigo->setFechaUso(new DateTime());
        $entityManager->flush();
    }

    /**
     * Genera un código aleatorio alfanumérico.
     *
     * @return string
     */
    private function generateRandomCode(): string {
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
    }

    /**
     * Busca un código de recuperación válido y usado recientemente por userId.
     *
     * @param int $userId
     * @param int $tipoRecuperacionId
     * @return CodigoUsuario|null
     */
    public function findValidRecoveryCodeByUserId(int $userId, int $tipoRecuperacionId): ?CodigoUsuario{
        return $this->createQueryBuilder('c')
            ->andWhere('c.usuario = :usuarioId')
            ->andWhere('c.tipo = :tipoCodigo')
            ->andWhere('c.usado = true')
            ->andWhere('c.fechaUso > :limite')
            ->setParameter('usuarioId', $userId)
            ->setParameter('tipoCodigo', $tipoRecuperacionId)
            ->setParameter('limite', (new DateTime())->modify('-10 minutes'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Genera un nuevo código de verificación.
     *
     * @param ContactoUsuario $contacto
     * @param TipoCodigoUsuario $tipoCodigo
     * @return CodigoUsuario
     */
    public function generateVerificationCode(ContactoUsuario $contacto, TipoCodigoUsuario $tipoCodigo): CodigoUsuario {
        $entityManager = $this->getEntityManager();

        $codigo = new CodigoUsuario();
        $codigo->setContacto($contacto);
        $codigo->setTipo($tipoCodigo);
        $codigo->setCodigo($this->generateRandomCode());
        $codigo->setFechaExpiracion((new DateTime())->modify('+5 minutes'));

        // Si necesitas asociarlo a un usuario:
        $usuario = $contacto->getUsuario();
        if ($usuario) {
            $codigo->setUsuario($usuario);
        }

        $entityManager->persist($codigo);
        $entityManager->flush();

        return $codigo;
    }
}