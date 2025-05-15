<?php

namespace itaxcix\services;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use itaxcix\models\dtos\SendVerificationCodeRequest;
use itaxcix\models\entities\usuario\CodigoUsuario;
use itaxcix\models\entities\usuario\ContactoUsuario;
use itaxcix\models\entities\usuario\TipoCodigoUsuario;
use itaxcix\services\notifications\NotificationServiceFactory;

class PerfilService {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationServiceFactory $notificationServiceFactory
    ) {}

    private function getContactoUsuarioRepository() {
        return $this->entityManager->getRepository(ContactoUsuario::class);
    }

    private function getTipoCodigoUsuarioRepository() {
        return $this->entityManager->getRepository(TipoCodigoUsuario::class);
    }

    private function getCodigoUsuarioRepository() {
        return $this->entityManager->getRepository(CodigoUsuario::class);
    }

    /**
     * Envía un código de verificación a un contacto.
     *
     * @param int $contactTypeId
     * @param string $contactValue
     * @return void
     * @throws Exception
     */
    public function sendVerificationCode(SendVerificationCodeRequest $dto): void {
        // Buscar contacto
        $contacto = $this->getContactoUsuarioRepository()->findByTypeAndValue($dto -> contactTypeId, $dto -> contact);
        if (!$contacto) {
            throw new Exception("Contacto no encontrado.", 404);
        }

        // Validar confirmación previa
        if ($contacto->isConfirmado()) {
            throw new Exception("El contacto ya está verificado.", 400);
        }

        // Obtener tipo "Verificación"
        $tipoVerificacion = $this->getTipoCodigoUsuarioRepository()->findOneByName('Verificación');
        if (!$tipoVerificacion) {
            throw new Exception("Tipo de código 'Verificación' no encontrado.", 500);
        }

        // Generar código
        $codigo = $this->getCodigoUsuarioRepository()->generateVerificationCode($contacto, $tipoVerificacion);

        // Enviar notificación
        $service = $this->notificationServiceFactory->getServiceForContactType($dto->contactTypeId);
        $service->send($contacto->getValor(), 'Código de verificación', $codigo->getCodigo(), 'verification');
    }

    /**
     * Verifica un código de contacto.
     *
     * @param string $code
     * @param int $contactTypeId
     * @param string $contactValue
     * @return array
     * @throws Exception
     */
    public function verifyContactCode(string $code, int $contactTypeId, string $contactValue): array {
        // Buscar contacto
        $contacto = $this->getContactoUsuarioRepository()->findByTypeAndValue($contactTypeId, $contactValue);
        if (!$contacto) {
            throw new Exception("Contacto no encontrado.", 404);
        }

        // Buscar tipo "Verificación"
        $tipoVerificacion = $this->getTipoCodigoUsuarioRepository()->findOneByName('Verificación');
        if (!$tipoVerificacion) {
            throw new Exception("Tipo de código 'Verificación' no encontrado.", 500);
        }

        // Buscar código válido
        $codigo = $this->getCodigoUsuarioRepository()->findValidCode($code, $contacto, $tipoVerificacion->getId());
        if (!$codigo) {
            throw new Exception("Código inválido o expirado.", 400);
        }

        // Confirmar contacto
        $contacto->setConfirmado(true);
        $this->getContactoUsuarioRepository()->save($contacto);

        // Marcar código como usado
        $this->getCodigoUsuarioRepository()->markAsUsed($codigo);

        return ['userId' => $codigo->getUsuario()->getId()];
    }
}