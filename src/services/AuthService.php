<?php

namespace itaxcix\services;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use itaxcix\models\dtos\LoginRequest;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\dtos\RegisterDriverRequest;
use itaxcix\models\entities\persona\Persona;
use itaxcix\models\entities\persona\TipoDocumento;
use itaxcix\models\entities\tuc\Empresa;
use itaxcix\models\entities\tuc\EstadoTuc;
use itaxcix\models\entities\tuc\ModalidadTuc;
use itaxcix\models\entities\tuc\RutaServicio;
use itaxcix\models\entities\tuc\TipoTramite;
use itaxcix\models\entities\tuc\TramiteTuc;
use itaxcix\models\entities\ubicacion\Departamento;
use itaxcix\models\entities\ubicacion\Distrito;
use itaxcix\models\entities\ubicacion\Provincia;
use itaxcix\models\entities\usuario\CodigoUsuario;
use itaxcix\models\entities\usuario\ContactoUsuario;
use itaxcix\models\entities\usuario\EstadoUsuario;
use itaxcix\models\entities\usuario\PerfilConductor;
use itaxcix\models\entities\usuario\Rol;
use itaxcix\models\entities\usuario\RolUsuario;
use itaxcix\models\entities\usuario\TipoCodigoUsuario;
use itaxcix\models\entities\usuario\TipoContacto;
use itaxcix\models\entities\usuario\Usuario;
use itaxcix\models\entities\vehiculo\CategoriaVehiculo;
use itaxcix\models\entities\vehiculo\ClaseVehiculo;
use itaxcix\models\entities\vehiculo\Color;
use itaxcix\models\entities\vehiculo\Marca;
use itaxcix\models\entities\vehiculo\Modelo;
use itaxcix\models\entities\vehiculo\TipoCombustible;
use itaxcix\models\entities\vehiculo\Vehiculo;
use itaxcix\validators\services\LoginValidator;

class AuthService {

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExternalApiService $externalApiService,
        private readonly JwtService $jwtService,
        private readonly LoginValidator $loginValidator
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

    /**
     * Login method to authenticate a user.
     *
     * @param LoginRequest $request
     * @return array
     * @throws Exception
     */
    public function login(LoginRequest $request): array {
        $usuario = $this->getUsuarioRepository()->validateCredentials($request->username, $request->password);
        $this->loginValidator->validateCredentials($usuario, $request->password);
        $roles = $this->getRolUsuarioRepository()->findActiveRolesByUsuario($usuario);
        $this->loginValidator->validateRoles($roles);
        $payload = [
            'id' => $usuario->getId(),
            'alias' => $usuario->getAlias(),
            'roles' => $roles,
        ];
        $token = $this->jwtService->generateToken($payload);

        return [
            'token' => $token,
            'user' => [
                'id' => $usuario->getId(),
                'alias' => $usuario->getAlias(),
                'roles' => $roles,
            ],
        ];
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
            $estadoUsuario = $this->getEstadoUsuarioRepository()->findByName('Activo')
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
                throw new Exception("El rol 'Ciudadano' no existe.", 500);
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

    public function registerDriver(RegisterDriverRequest $request): array
    {
        $this->entityManager->beginTransaction();

        try {
            // 1. Validar persona
            $tipoDocumento = $this->getTipoDocumentoRepository()->findById($request->documentType);
            if (!$tipoDocumento) {
                throw new Exception('Tipo de documento no válido.', 400);
            }

            $existingPerson = $this->getPersonaRepository()->findByDocument($request->documentNumber);
            if (!$existingPerson) {
                $externalData = $this->externalApiService->getPerson($request->documentType, $request->documentNumber);
                if (!$externalData) {
                    throw new Exception('No se encontraron datos de persona en la API externa.', 422);
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

            // 2. Validar usuario existente
            $existingUserForPerson = $this->getUsuarioRepository()->findByPerson($persona);
            if ($existingUserForPerson) {
                throw new Exception("Ya existe un usuario registrado para esta persona.", 400);
            }
            if ($this->getUsuarioRepository()->findByAlias($request->alias)) {
                throw new Exception('Alias ya está en uso.', 400);
            }

            // 3. Validar TUC (placa)
            $tucData = $this->externalApiService->getVehicleTUC($request->licensePlate);
            if (!$tucData) {
                throw new Exception('No se encontró la TUC (placa) en la API externa.', 422);
            }

            // 4. Registrar o reutilizar datos maestros (marca, modelo, color, clase, categoría)
            $marcaRepo = $this->entityManager->getRepository(Marca::class);
            $modeloRepo = $this->entityManager->getRepository(Modelo::class);
            $colorRepo = $this->entityManager->getRepository(Color::class);
            $claseRepo = $this->entityManager->getRepository(ClaseVehiculo::class);
            $categoriaRepo = $this->entityManager->getRepository(CategoriaVehiculo::class);
            $tipoCombustibleRepo = $this->entityManager->getRepository(TipoCombustible::class);

            $marca = $marcaRepo->findOneBy(['nombre' => $tucData['MARCA']]);
            if (!$marca) {
                $marca = new Marca();
                $marca->setNombre($tucData['MARCA']);
                $this->entityManager->persist($marca);
                $this->entityManager->flush();
            }

            $modelo = $modeloRepo->findOneBy(['nombre' => $tucData['MODELO'], 'marca' => $marca]);
            if (!$modelo) {
                $modelo = new Modelo();
                $modelo->setNombre($tucData['MODELO']);
                $modelo->setMarca($marca);
                $this->entityManager->persist($modelo);
                $this->entityManager->flush();
            }

            $color = $colorRepo->findOneBy(['nombre' => $tucData['COLOR']]);
            if (!$color) {
                $color = new Color();
                $color->setNombre($tucData['COLOR']);
                $this->entityManager->persist($color);
                $this->entityManager->flush();
            }

            $clase = $claseRepo->findOneBy(['nombre' => $tucData['CLASE']]);
            if (!$clase) {
                $clase = new ClaseVehiculo();
                $clase->setNombre($tucData['CLASE']);
                $this->entityManager->persist($clase);
                $this->entityManager->flush();
            }

            $categoria = $categoriaRepo->findOneBy(['nombre' => $tucData['CATEGORIA']]);
            if (!$categoria) {
                $categoria = new CategoriaVehiculo();
                $categoria->setNombre($tucData['CATEGORIA']);
                $this->entityManager->persist($categoria);
                $this->entityManager->flush();
            }

            $tipoCombustible = $tipoCombustibleRepo->findOneBy(['nombre' => $tucData['TIPO_COMBUST']]);
            if (!$tipoCombustible) {
                $tipoCombustible = new TipoCombustible();
                $tipoCombustible->setNombre($tucData['TIPO_COMBUST']);
                $this->entityManager->persist($tipoCombustible);
                $this->entityManager->flush();
            }

            // 5. Registrar vehículo si no existe
            $vehiculoRepo = $this->entityManager->getRepository(Vehiculo::class);
            $vehiculo = $vehiculoRepo->findOneBy(['placa' => $tucData['PLACA']]);
            if (!$vehiculo) {
                $vehiculo = new Vehiculo();
                $vehiculo->setPlaca($tucData['PLACA']);
                $vehiculo->setModelo($modelo);
                $vehiculo->setColor($color);
                $vehiculo->setAnioFabricacion((int)$tucData['ANIO_FABRIC']);
                $vehiculo->setNumeroAsientos((int)$tucData['NUM_ASIENTOS']);
                $vehiculo->setNumeroPasajeros((int)$tucData['NUM_PASAJ']);
                $vehiculo->setTipoCombustible($tipoCombustible);
                $vehiculo->setClase($clase);
                $vehiculo->setCategoria($categoria);
                $this->entityManager->persist($vehiculo);
                $this->entityManager->flush();
            }

            // 6. Registrar ubicación (departamento, provincia, distrito)
            $departamentoRepo = $this->entityManager->getRepository(Departamento::class);
            $provinciaRepo = $this->entityManager->getRepository(Provincia::class);
            $distritoRepo = $this->entityManager->getRepository(Distrito::class);

            $departamento = $departamentoRepo->findOneBy(['nombre' => $tucData['DEPARTAMENTO']]);
            if (!$departamento) {
                $departamento = new Departamento();
                $departamento->setNombre($tucData['DEPARTAMENTO']);
                $departamento->setUbigeo(substr($tucData['UBIGEO'], 0, 2) . '0000');
                $this->entityManager->persist($departamento);
                $this->entityManager->flush();
            }

            $provincia = $provinciaRepo->findOneBy(['nombre' => $tucData['PROVINCIA'], 'departamento' => $departamento]);
            if (!$provincia) {
                $provincia = new Provincia();
                $provincia->setNombre($tucData['PROVINCIA']);
                $provincia->setDepartamento($departamento);
                $provincia->setUbigeo(substr($tucData['UBIGEO'], 0, 4) . '00');
                $this->entityManager->persist($provincia);
                $this->entityManager->flush();
            }

            $distrito = $distritoRepo->findOneBy(['nombre' => $tucData['DISTRITO'], 'provincia' => $provincia]);
            if (!$distrito) {
                $distrito = new Distrito();
                $distrito->setNombre($tucData['DISTRITO']);
                $distrito->setProvincia($provincia);
                $distrito->setUbigeo($tucData['UBIGEO']);
                $this->entityManager->persist($distrito);
                $this->entityManager->flush();
            }

            // 7. Registrar empresa, modalidad, tipo de tramite, estado TUC
            $empresaRepo = $this->entityManager->getRepository(Empresa::class);
            $modalidadRepo = $this->entityManager->getRepository(ModalidadTuc::class);
            $tipoTramiteRepo = $this->entityManager->getRepository(TipoTramite::class);
            $estadoTucRepo = $this->entityManager->getRepository(EstadoTuc::class);

            $empresa = $empresaRepo->findOneBy(['ruc' => $tucData['RUC_EMPRESA']]);
            if (!$empresa) {
                $empresa = new Empresa();
                $empresa->setRuc($tucData['RUC_EMPRESA']);
                $empresa->setNombre($tucData['RUC_EMPRESA']); // O usa otro campo si tienes el nombre real
                $this->entityManager->persist($empresa);
                $this->entityManager->flush();
            }

            $modalidad = $modalidadRepo->findOneBy(['nombre' => $tucData['MODALIDAD']]);
            if (!$modalidad) {
                $modalidad = new ModalidadTuc();
                $modalidad->setNombre($tucData['MODALIDAD']);
                $this->entityManager->persist($modalidad);
                $this->entityManager->flush();
            }

            $tipoTramite = $tipoTramiteRepo->findOneBy(['nombre' => $tucData['TRAMITE']]);
            if (!$tipoTramite) {
                $tipoTramite = new TipoTramite();
                $tipoTramite->setNombre($tucData['TRAMITE']);
                $this->entityManager->persist($tipoTramite);
                $this->entityManager->flush();
            }

            $estadoTuc = $estadoTucRepo->findOneBy(['nombre' => 'Activo']);
            if (!$estadoTuc) {
                throw new Exception("No existe el estado TUC 'Activo'", 500);
            }

            // 6. Registrar usuario
            $estadoUsuario = $this->getEstadoUsuarioRepository()->findByName('Activo')
                ?? $this->getEstadoUsuarioRepository()->getDefault();
            if (!$estadoUsuario) {
                throw new Exception('No hay estados de usuario configurados.', 500);
            }

            $usuario = new Usuario();
            $usuario->setAlias($request->alias);
            $usuario->setClave(password_hash($request->password, PASSWORD_DEFAULT));
            $usuario->setPersona($persona);
            $usuario->setEstado($estadoUsuario);
            $this->entityManager->persist($usuario);
            $this->entityManager->flush();

            // 7. Asignar rol Conductor
            $rol = $this->getRolRepository()->findOneBy(['nombre' => 'Conductor']);
            if (!$rol) {
                throw new Exception("El rol 'Conductor' no existe.", 500);
            }
            $rolUsuario = new RolUsuario();
            $rolUsuario->setUsuario($usuario);
            $rolUsuario->setRol($rol);
            $rolUsuario->setActivo(true);
            $this->entityManager->persist($rolUsuario);
            $this->entityManager->flush();

            $perfilConductor = new PerfilConductor();
            $perfilConductor->setUsuario($usuario);
            $perfilConductor->setDisponible(false); // o true si lo deseas disponible por defecto
            $this->entityManager->persist($perfilConductor);
            $this->entityManager->flush();

            // 8. Registrar tramite TUC
            $fechaTram = isset($tucData['FECHA_TRAM']) ? DateTime::createFromFormat('Ymd', $tucData['FECHA_TRAM']) : null;
            $fechaEmi = isset($tucData['FECHA_EMI']) ? DateTime::createFromFormat('Ymd', $tucData['FECHA_EMI']) : null;
            $fechaCaduc = isset($tucData['FECHA_CADUC']) ? DateTime::createFromFormat('Ymd', $tucData['FECHA_CADUC']) : null;

            $tramiteTuc = new TramiteTuc();
            $tramiteTuc->setUsuario($usuario);
            $tramiteTuc->setVehiculo($vehiculo);
            $tramiteTuc->setEmpresa($empresa);
            $tramiteTuc->setDistrito($distrito);
            $tramiteTuc->setEstado($estadoTuc);
            $tramiteTuc->setTipo($tipoTramite);
            $tramiteTuc->setModalidad($modalidad);
            if ($fechaTram) $tramiteTuc->setFechaTramite($fechaTram);
            if ($fechaEmi) $tramiteTuc->setFechaEmision($fechaEmi);
            if ($fechaCaduc) $tramiteTuc->setFechaCaducidad($fechaCaduc);

            // Puedes setear otros campos como fechas, ruta, etc. según tu entidad
            $this->entityManager->persist($tramiteTuc);
            $this->entityManager->flush();

            // 9. Registrar ruta de servicio
            $rutaServicioRepo = $this->entityManager->getRepository(RutaServicio::class);
            $rutaServicio = $rutaServicioRepo->findOneBy(['tramite' => $tramiteTuc, 'texto' => $tucData['RUTA']]);
            if (!$rutaServicio) {
                $rutaServicio = new RutaServicio();
                $rutaServicio->setTramite($tramiteTuc);
                $rutaServicio->setTexto($tucData['RUTA']);
                $rutaServicio->setTipoServicio($tucData['TIP_SERV'] ?? null); // <-- Aquí se asigna TIP_SERV
                $this->entityManager->persist($rutaServicio);
                $this->entityManager->flush();
            }

            // 8. Registrar contacto
            $contactValue = $request->contactMethod['email'] ?? $request->contactMethod['phone'];
            $tipoContactoNombre = isset($request->contactMethod['email']) ? 'Correo Electrónico' : 'Teléfono Móvil';
            $existingContact = $this->entityManager->getRepository(ContactoUsuario::class)->findOneBy([
                'valor' => $contactValue
            ]);
            if ($existingContact) {
                throw new Exception("El contacto '{$contactValue}' ya está en uso.", 400);
            }
            $tipoContacto = $this->getTipoContactoRepository()->findByName($tipoContactoNombre);
            if (!$tipoContacto) {
                throw new Exception('Tipo de contacto no válido.', 500);
            }
            $contacto = new ContactoUsuario();
            $contacto->setUsuario($usuario);
            $contacto->setTipo($tipoContacto);
            $contacto->setValor($contactValue);
            $contacto->setConfirmado(false);
            $this->entityManager->persist($contacto);
            $this->entityManager->flush();

            $this->entityManager->commit();

            return [
                'message' => 'Conductor registrado correctamente.',
                'userId' => $usuario->getId(),
                'personId' => $persona->getId(),
                'vehicleId' => $vehiculo->getId()
            ];

        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function requestRecoveryByEmail(string $email): void
    {
        $contacto = $this->findContacto($email); // Usa el nuevo método

        // 2. Generar código aleatorio
        $codigo = $this->generateVerificationCode(); // Ej: "ABC123"

        // 3. Guardar código en tb_codigo_usuario
        $codigoEntity = new CodigoUsuario();
        $codigoEntity->setUsuario($contacto->getUsuario());
        $codigoEntity->setCodigo($codigo);
        $codigoEntity->setFechaExpiracion((new DateTime())->modify('+15 minutes'));
        $codigoEntity->setTipo($this->getCodigoTipo('Recuperación'));
        $codigoEntity->setContacto($contacto);

        $this->entityManager->persist($codigoEntity);
        $this->entityManager->flush();

        // 4. Simular envío del código por correo
        $this->sendRecoveryEmail($email, $codigo);
    }

    private function generateVerificationCode(): string
    {
        return strtoupper(substr(md5(random_bytes(10)), 0, 6));
    }

    private function sendRecoveryEmail(string $email, string $codigo): void
    {
        // Aquí iría el envío real del correo
        error_log("Enviando código de recuperación a $email: $codigo");
    }

    public function verifyCode(string $code, ?string $email = null, ?string $phone = null): array
    {
        try {
            $contacto = $this->findContacto($email, $phone);

            // Este método ahora hace toda la validación
            $codigo = $this->findCodigo($code, $contacto);

            // Marcar como usado
            $codigo->setUsado(true);
            $this->entityManager->flush();

            return [
                'userId' => $contacto->getUsuario()->getId()
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function resetPassword(int $userId, string $newPassword): void
    {
        $usuario = $this->entityManager->find(Usuario::class, $userId);

        if (!$usuario) {
            throw new Exception("Usuario no encontrado", 404);
        }

        // Validar contraseña
        $this->validatePassword($newPassword);

        // Actualizar contraseña
        $usuario->setClave(password_hash($newPassword, PASSWORD_DEFAULT));
        $this->entityManager->flush();
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres.", 400);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra mayúscula.", 400);
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra minúscula.", 400);
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un número.", 400);
        }

        if (!preg_match('/[\W_]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un carácter especial.", 400);
        }
    }

    public function requestRecoveryByPhone(string $phone): void
    {
        $contacto = $this->findContacto(null, $phone); // Usa el nuevo método

        // Generar código
        $codigo = strtoupper(substr(md5(random_bytes(10)), 0, 6));
        $now = new DateTime();

        // Crear entidad
        $codigoEntity = new CodigoUsuario();
        $codigoEntity->setUsuario($contacto->getUsuario());
        $codigoEntity->setCodigo($codigo);
        $codigoEntity->setFechaExpiracion((new DateTime())->modify('+15 minutes'));
        $codigoEntity->setUsado(false);
        $codigoEntity->setContacto($contacto);
        $codigoEntity->setTipo($this->getCodigoTipo('Recuperación'));

        $this->entityManager->persist($codigoEntity);
        $this->entityManager->flush();

        // Aquí iría el envío real del SMS
        error_log("Enviando código de recuperación al teléfono $phone: $codigo");
    }

    private function getCodigoTipo(string $nombre): TipoCodigoUsuario
    {
        $repo = $this->entityManager->getRepository(TipoCodigoUsuario::class);
        $tipo = $repo->findOneBy(['nombre' => $nombre]);

        if (!$tipo) {
            throw new Exception("Tipo de código '$nombre' no encontrado", 400);
        }

        return $tipo;
    }

    private function findContacto(?string $email = null, ?string $phone = null, bool $activoOnly = true): ?ContactoUsuario
    {
        if (!$email && !$phone) {
            throw new Exception("Se requiere al menos un email o número de teléfono.", 400);
        }

        $contactoRepo = $this->entityManager->getRepository(ContactoUsuario::class);

        $criteria = [];

        if ($email) {
            $criteria['valor'] = $email;
        } elseif ($phone) {
            $criteria['valor'] = $phone;
        }

        if ($activoOnly) {
            $criteria['activo'] = true;
        }

        $contacto = $contactoRepo->findOneBy($criteria);

        if (!$contacto) {
            $tipo = $email ? 'correo electrónico' : 'teléfono';
            throw new Exception("No se encontró ningún contacto con ese $tipo.", 404);
        }

        return $contacto;
    }

    /**
     * @throws Exception
     */
    private function findCodigo(string $code, ContactoUsuario $contacto): ?CodigoUsuario
    {
        if (empty(trim($code))) {
            throw new Exception("El código es obligatorio.", 400);
        }

        $codigoRepo = $this->entityManager->getRepository(CodigoUsuario::class);
        $codigo = $codigoRepo->findOneBy([
            'contacto' => $contacto,
            'codigo' => $code
        ]);

        if (!$codigo) {
            throw new Exception("Código inválido o no encontrado", 400);
        }

        if ($codigo->isUsado()) {
            throw new Exception("Este código ya fue utilizado", 400);
        }

        if ($codigo->getFechaExpiracion() === null) {
            throw new Exception("Código sin fecha de expiración", 400);
        }

        $now = new \DateTime('now', new \DateTimeZone('America/Caracas'));
        $expiracion = clone $now;
        $expiracion->modify('+15 minutes');

        return $codigo;
    }
}