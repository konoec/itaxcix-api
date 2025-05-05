<?php

namespace itaxcix\services\usuario;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\entities\persona\Persona;
use itaxcix\models\entities\persona\TipoDocumento;
use itaxcix\models\entities\usuario\ContactoUsuario;
use itaxcix\models\entities\usuario\EstadoUsuario;
use itaxcix\models\entities\usuario\Rol;
use itaxcix\models\entities\usuario\RolUsuario;
use itaxcix\models\entities\usuario\TipoContacto;
use itaxcix\models\entities\usuario\Usuario;
use itaxcix\services\utils\ExternalApiService;

class UsuarioService {

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExternalApiService $externalApiService
    ) {}

    private function getPersonaRepository(): EntityRepository {
        return $this->entityManager->getRepository(Persona::class);
    }

    private function getTipoDocumentoRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoDocumento::class);
    }

    private function getUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(Usuario::class);
    }

    private function getEstadoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(EstadoUsuario::class);
    }

    private function getTipoContactoRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoContacto::class);
    }

    private function getRolRepository(): EntityRepository {
        return $this->entityManager->getRepository(Rol::class);
    }

    private function getRolUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(RolUsuario::class);
    }

    public function registerCitizen(RegisterCitizenRequest $request): array
    {
        $this->entityManager->beginTransaction();

        try {
            // Ya está validado, puedes usar directamente los campos:
            $tipoDocumento = $this->getTipoDocumentoRepository()->findById($request->documentType);
            if (!$tipoDocumento) {
                throw new Exception('Tipo de documento no válido.', 400);
            }

            // Verificar si ya existe persona por documento
            $existingPerson = $this->getPersonaRepository()->findByDocument($request->documentNumber);

            if (!$existingPerson) {
                $externalData = $this->externalApiService->getPerson($request->documentType, $request->documentNumber);
                if (!$externalData) {
                    throw new Exception('No se encontraron datos en la API externa.', 422);
                }

                $persona = new Persona();
                $persona->setNombre($externalData['nombre'] ?? null);
                $persona->setApellido($externalData['apellido'] ?? null);
                $persona->setDocumento($request->documentNumber);
                $persona->setTipoDocumento($tipoDocumento);
                $this->entityManager->persist($persona);
                $this->entityManager->flush();
            } else {
                $persona = $existingPerson;
            }

            // Verificar si ya tiene usuario
            $existingUserForPerson = $this->getUsuarioRepository()->findByPerson($persona);
            if ($existingUserForPerson) {
                throw new Exception("Ya existe un usuario registrado para esta persona.", 400);
            }

            // Verificar alias disponible
            if ($this->getUsuarioRepository()->findByAlias($request->alias)) {
                throw new Exception('Alias ya está en uso.', 400);
            }

            // Obtener estado por defecto
            $estadoUsuario = $this->getEstadoUsuarioRepository()->findByName('Pendiente de Confirmación')
                ?? $this->getEstadoUsuarioRepository()->getDefault();

            if (!$estadoUsuario) {
                throw new Exception('No hay estados de usuario configurados.', 500);
            }

            // Registrar usuario
            $usuario = new Usuario();
            $usuario->setAlias($request->alias);
            $usuario->setClave(password_hash($request->password, PASSWORD_DEFAULT));
            $usuario->setPersona($persona);
            $usuario->setEstado($estadoUsuario);
            $this->entityManager->persist($usuario);
            $this->entityManager->flush();

            // Buscar el rol "Ciudadano"
            $rol = $this->getRolRepository()->findOneBy(['nombre' => 'Ciudadano']);
            if (!$rol) {
                throw new \Exception("El rol 'Ciudadano' no existe.", 500);
            }

            $rolUsuario = new RolUsuario();
            $rolUsuario->setUsuario($usuario);
            $rolUsuario->setRol($rol);
            $rolUsuario->setActivo(true);

            $this->entityManager->persist($rolUsuario);
            $this->entityManager->flush();

            // Registrar contacto
            $contactValue = $request->contactMethod['email'] ?? $request->contactMethod['phone'];
            $tipoContactoNombre = isset($request->contactMethod['email']) ? 'Correo Electrónico' : 'Teléfono Móvil';

            // Verificar si ese contacto ya está en uso
            $existingContact = $this->entityManager->getRepository(ContactoUsuario::class)->findOneBy([
                'valor' => $contactValue
            ]);

            if ($existingContact) {
                throw new Exception("El contacto '{$contactValue}' ya está en uso.", 400);
            }

            // Obtener tipo de contacto
            $tipoContacto = $this->getTipoContactoRepository()->findByName($tipoContactoNombre);
            if (!$tipoContacto) {
                throw new Exception('Tipo de contacto no válido.', 500);
            }

            // Guardar contacto
            $contacto = new ContactoUsuario();
            $contacto->setUsuario($usuario);
            $contacto->setTipo($tipoContacto);
            $contacto->setValor($contactValue);
            $contacto->setConfirmado(false);
            $this->entityManager->persist($contacto);
            $this->entityManager->flush();

            $this->entityManager->commit();

            return [
                'message' => 'Usuario registrado correctamente.',
                'userId' => $usuario->getId(),
                'personId' => $persona->getId()
            ];

        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}