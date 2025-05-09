<?php

namespace itaxcix\services;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use itaxcix\models\dtos\LoginRequest;
use itaxcix\models\dtos\RecoveryRequest;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\dtos\RegisterDriverRequest;
use itaxcix\models\entities\persona\Persona;
use itaxcix\models\entities\persona\TipoDocumento;
use itaxcix\models\entities\tuc\Empresa;
use itaxcix\models\entities\tuc\EstadoTuc;
use itaxcix\models\entities\tuc\ModalidadTuc;
use itaxcix\models\entities\tuc\RutaServicio;
use itaxcix\models\entities\tuc\TipoServicio;
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
use itaxcix\models\entities\usuario\VehiculoUsuario;
use itaxcix\models\entities\vehiculo\CategoriaVehiculo;
use itaxcix\models\entities\vehiculo\ClaseVehiculo;
use itaxcix\models\entities\vehiculo\Color;
use itaxcix\models\entities\vehiculo\EspecificacionTecnica;
use itaxcix\models\entities\vehiculo\Marca;
use itaxcix\models\entities\vehiculo\Modelo;
use itaxcix\models\entities\vehiculo\TipoCombustible;
use itaxcix\models\entities\vehiculo\Vehiculo;
use itaxcix\services\notifications\NotificationServiceFactory;
use itaxcix\services\notifications\NotificationServiceInterface;
use itaxcix\utils\StringUtils;

class AuthService {

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MunicipalidadService $municipalidadService,
        private readonly ExternalService $externalService,
        private readonly JwtService $jwtService,
        private readonly StringUtils $stringUtils,
        private readonly NotificationServiceFactory $notificationServiceFactory
    ) {}

    private function getContactoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(ContactoUsuario::class);
    }
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
    private function getVehiculoRepository(): EntityRepository {
        return $this->entityManager->getRepository(Vehiculo::class);
    }
    private function getDepartamentoRepository(): EntityRepository {
        return $this->entityManager->getRepository(Departamento::class);
    }
    private function getProvinciaRepository(): EntityRepository {
        return $this->entityManager->getRepository(Provincia::class);
    }
    private function getDistritoRepository(): EntityRepository {
        return $this->entityManager->getRepository(Distrito::class);
    }
    private function getMarcaRepository(): EntityRepository {
        return $this->entityManager->getRepository(Marca::class);
    }
    private function getModeloRepository(): EntityRepository {
        return $this->entityManager->getRepository(Modelo::class);
    }
    private function getColorRepository(): EntityRepository {
        return $this->entityManager->getRepository(Color::class);
    }
    private function getClaseVehiculoRepository(): EntityRepository {
        return $this->entityManager->getRepository(ClaseVehiculo::class);
    }
    private function getCategoriaVehiculoRepository(): EntityRepository {
        return $this->entityManager->getRepository(CategoriaVehiculo::class);
    }
    private function getTipoCombustibleRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoCombustible::class);
    }
    private function getEmpresaRepository(): EntityRepository {
        return $this->entityManager->getRepository(Empresa::class);
    }
    private function getModalidadTucRepository(): EntityRepository {
        return $this->entityManager->getRepository(ModalidadTuc::class);
    }
    private function getTipoServicioRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoServicio::class);
    }
    private function getTipoTramiteRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoTramite::class);
    }
    private function getEstadoTucRepository(): EntityRepository {
        return $this->entityManager->getRepository(EstadoTuc::class);
    }
    private function getTramiteTucRepository(): EntityRepository {
        return $this->entityManager->getRepository(TramiteTuc::class);
    }
    private function getPerfilConductorRepository(): EntityRepository {
        return $this->entityManager->getRepository(PerfilConductor::class);
    }
    private function getVehiculoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(VehiculoUsuario::class);
    }
    private function getRutaServicioRepository(): EntityRepository {
        return $this->entityManager->getRepository(RutaServicio::class);
    }
    private function getEspecificacionTecnicaRepository(): EntityRepository {
        return $this->entityManager->getRepository(EspecificacionTecnica::class);
    }
    private function getCodigoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(CodigoUsuario::class);
    }
    private function getTipoCodigoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(TipoCodigoUsuario::class);
    }

    /**
     * Login method to authenticate a user.
     *
     * @param LoginRequest $request
     * @return array
     * @throws Exception
     */
    public function login(LoginRequest $request): array {
        // 1. Validar credenciales: esto lo hace el repositorio internamente
        $usuario = $this->getUsuarioRepository()->validateCredentials($request->username, $request->password);

        // 2. Si no se encontró usuario válido, lanzar excepción
        if (!$usuario) {
            throw new Exception("Credenciales inválidas.", 401);
        }

        // 3. Obtener roles activos del usuario
        $roles = $this->getRolUsuarioRepository()->findActiveRolesByUsuario($usuario);

        // 4. Validar que tenga al menos un rol asignado
        if (empty($roles)) {
            throw new Exception("El usuario no tiene roles asignados.", 403);
        }

        // 5. Generar payload para JWT
        $payload = [
            'id' => $usuario->getId(),
            'alias' => $usuario->getAlias(),
            'roles' => $roles,
        ];

        // 6. Generar token JWT
        $token = $this->jwtService->generateToken($payload);

        // 7. Retornar respuesta final
        return [
            'token' => $token,
            'user' => [
                'id' => $usuario->getId(),
                'alias' => $usuario->getAlias(),
                'roles' => $roles,
            ],
        ];
    }

    /**
     * Registra un ciudadano en el sistema.
     *
     * Este servicio realiza validaciones y crea las entidades necesarias
     * para registrar un ciudadano en la base de datos.
     *
     * @param RegisterCitizenRequest $dto Datos necesarios para el registro.
     * @return array Información del usuario registrado.
     * @throws Exception Sí ocurre algún error durante el proceso.
     */
    public function registerCitizen(RegisterCitizenRequest $dto): array {
        $this->entityManager->beginTransaction();

        try {
            // 1. Validar si ya existe una persona con ese documento
            if ($this->getPersonaRepository()->existsByDocumento($dto->document)) {
                throw new Exception("Ya existe una persona con ese documento.", 400);
            }

            // 2. Validar si ya existe alias
            if ($this->getUsuarioRepository()->existsByAlias($dto->alias)) {
                throw new Exception("El alias ya está en uso.", 400);
            }

            // 3. Validar si ya existe contacto
            if ($this->getContactoUsuarioRepository()->existsByValor($dto->contact)) {
                throw new Exception("El contacto ya está registrado.", 400);
            }

            // 4. Obtener tipo de documento
            $tipoDocumento = $this->getTipoDocumentoRepository()->getById($dto->documentTypeId);
            if (!$tipoDocumento || !$tipoDocumento->isActivo()) {
                throw new Exception("Tipo de documento inválido o inactivo.", 400);
            }

            // 5. Obtener tipo de contacto
            $tipoContacto = $this->getTipoContactoRepository()->getById($dto->contactTypeId);
            if (!$tipoContacto || !$tipoContacto->isActivo()) {
                throw new Exception("Tipo de contacto inválido o inactivo.", 400);
            }

            // 6. Validar con Reniec (ahora después de verificar unicidad)
            $reniecResponse = $this->externalService->getPerson(
                (string)$dto->documentTypeId,
                $dto->document
            );

            if (!$reniecResponse['success']) {
                throw new Exception("Documento no válido o no encontrado en Reniec.", 400);
            }

            $personaReniec = $reniecResponse['data'];

            // 7. Crear Persona usando datos de Reniec
            $persona = new Persona();
            $persona->setDocumento($dto->document);
            $persona->setTipoDocumento($tipoDocumento);

            // Extraer nombres y apellidos de Reniec
            if (isset($personaReniec['nombre_completo'])) {
                $nombresApellidos = $this->stringUtils->splitFullName($personaReniec['nombre_completo']);
                $persona->setNombre($nombresApellidos['nombres']);
                $persona->setApellido($nombresApellidos['apellidos']);
            } else if (isset($personaReniec['razon_social'])) {
                // En caso de empresas (RUC)
                $persona->setNombre($personaReniec['razon_social']);
                $persona->setApellido('');
            }

            $persona->setActivo(true);

            $this->getPersonaRepository()->save($persona);

            // 8. Obtener estado 'Activo' (ID 1)
            $estadoActivo = $this->getEstadoUsuarioRepository()->getById(1);
            if (!$estadoActivo) {
                throw new Exception("No se encontró el estado 'Activo'.", 500);
            }

            // 9. Crear Usuario
            $usuario = new Usuario();
            $usuario->setAlias($dto->alias);
            $usuario->setClave(password_hash($dto->password, PASSWORD_DEFAULT));
            $usuario->setPersona($persona);
            $usuario->setEstado($estadoActivo);

            $this->getUsuarioRepository()->save($usuario);

            // 10. Registrar Contacto del usuario
            $contacto = new ContactoUsuario();
            $contacto->setUsuario($usuario);
            $contacto->setTipo($tipoContacto);
            $contacto->setValor($dto->contact);
            $contacto->setConfirmado(false);
            $contacto->setActivo(true);

            $this->getContactoUsuarioRepository()->save($contacto);

            // 11. Registrar Rol de Ciudadano
            $rolCiudadano = $this->getRolRepository()->getByNombre('Ciudadano');
            if (!$rolCiudadano || !$rolCiudadano->isActivo()) {
                throw new Exception("No se encontró el rol 'Ciudadano'.", 500);
            }

            $rolUsuario = new RolUsuario();
            $rolUsuario->setUsuario($usuario);
            $rolUsuario->setRol($rolCiudadano);
            $rolUsuario->setActivo(true);

            $this->getRolUsuarioRepository()->save($rolUsuario);

            // Confirmar transacción
            $this->entityManager->commit();

            return [
                'usuarioId' => $usuario->getId(),
                'alias' => $usuario->getAlias(),
                'mensaje' => 'Registro exitoso'
            ];

        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Registra un conductor en el sistema.
     *
     * Este servicio realiza validaciones y crea las entidades necesarias
     * para registrar un conductor en la base de datos.
     *
     * @param RegisterDriverRequest $dto Datos necesarios para el registro.
     * @return array Información del usuario registrado.
     * @throws Exception Sí ocurre algún error durante el proceso.
     */
    public function registerDriver(RegisterDriverRequest $dto): array {
        $this->entityManager->beginTransaction();

        try {
            // Bloque 1: Validaciones iniciales
            if ($this->getPersonaRepository()->existsByDocumento($dto->document)) {
                throw new Exception("Ya existe una persona con ese documento.", 400);
            }
            if ($this->getUsuarioRepository()->existsByAlias($dto->alias)) {
                throw new Exception("El alias ya está en uso.", 400);
            }
            if ($this->getContactoUsuarioRepository()->existsByValor($dto->contact)) {
                throw new Exception("El contacto ya está registrado.", 400);
            }
            if ($this->getVehiculoRepository()->existsByPlaca($dto->licensePlate)) {
                throw new Exception("Ya existe un vehículo con esa placa.", 400);
            }

            // Bloque 2: Obtener tipos básicos
            $tipoDocumento = $this->getTipoDocumentoRepository()->getById($dto->documentTypeId);
            if (!$tipoDocumento || !$tipoDocumento->isActivo()) {
                throw new Exception("Tipo de documento inválido o inactivo.", 400);
            }

            $tipoContacto = $this->getTipoContactoRepository()->getById($dto->contactTypeId);
            if (!$tipoContacto || !$tipoContacto->isActivo()) {
                throw new Exception("Tipo de contacto inválido o inactivo.", 400);
            }

            // Bloque 3: Llamadas a API externas (usar mocks por ahora)
            $reniecResponse = $this->externalService->getPerson(
                (string)$dto->documentTypeId,
                $dto->document
            );

            if (!$reniecResponse['success']) {
                throw new Exception("Documento no válido o no encontrado en Reniec.", 400);
            }

            $vehiculoData = $this->municipalidadService->getVehicleTUC(
                $dto->licensePlate
            );

            if (!$vehiculoData) {
                throw new Exception("Placa no válida o no encontrada en la información municipal.", 400);
            }

            // Bloque 4: Procesar Ubigeo desde el mock
            $ubigeoDistrito = $vehiculoData['UBIGEO']; // '140101'
            $ubigeoProvincia = substr($ubigeoDistrito, 0, 4) . '00'; // '140100'
            $ubigeoDepartamento = substr($ubigeoDistrito, 0, 2) . '0000'; // '140000'

            // Bloque 5: Crear o validar Departamento, Provincia, Distrito
            $departamentoNombre = ucwords(strtolower($vehiculoData['DEPARTAMENTO'])); // Lambayeque
            $provinciaNombre = ucwords(strtolower($vehiculoData['PROVINCIA']));       // Chiclayo
            $distritoNombre = ucwords(strtolower($vehiculoData['DISTRITO']));         // Chiclayo

            $departamento = $this->getDepartamentoRepository()->getOrCreateUbigeoOrName(
                $ubigeoDepartamento,
                $departamentoNombre
            );
            $provincia = $this->getProvinciaRepository()->getOrCreateUbigeoOrName(
                $ubigeoProvincia,
                $provinciaNombre,
                $departamento
            );
            $distrito = $this->getDistritoRepository()->getOrCreateUbigeoOrName(
                $ubigeoDistrito,
                $distritoNombre,
                $provincia
            );

            // Bloque 6: Crear o validar Marca, Modelo, Color
            $marcaNombre = ucwords(strtolower($vehiculoData['MARCA'])); // Joylong
            $modeloNombre = ucwords(strtolower($vehiculoData['MODELO'])); // Hkl6600
            $colorNombre = ucwords(strtolower($vehiculoData['COLOR'])); // Blanco

            $marca = $this->getMarcaRepository()->getOrCreateByName($marcaNombre);
            $modelo = $this->getModeloRepository()->getOrCreateByNameAndMarca($modeloNombre, $marca);
            $color = $this->getColorRepository()->getOrCreateByName($colorNombre);

            // Bloque 7: Crear o validar Clase, Categoría, Combustible
            $claseNombre = ucwords(strtolower($vehiculoData['CLASE'])); // Combi
            $categoriaNombre = ucwords(strtolower($vehiculoData['CATEGORIA'])); // M2
            $combustibleNombre = ucwords(strtolower($vehiculoData['TIPO_COMBUST'])); // Bi-combustible

            $clase = $this->getClaseVehiculoRepository()->getOrCreateByName($claseNombre);
            $categoria = $this->getCategoriaVehiculoRepository()->getOrCreateByName($categoriaNombre);
            $tipoCombustible = $this->getTipoCombustibleRepository()->getOrCreateByName($combustibleNombre);

            // Bloque 8: Validar/crear Empresa usando RUC_EMPRESA del vehículo
            $empresaRuc = $vehiculoData['RUC_EMPRESA'];

            // Llamar al servicio externo para obtener datos reales de la empresa
            $rucResponse = $this->externalService->getPerson('4', $empresaRuc); // Tipo '4' = RUC

            if (!$rucResponse['success']) {
                throw new Exception("No se encontraron datos válidos para el RUC: $empresaRuc", 400);
            }

            $rucData = $rucResponse['data'];
            $razonSocial = $rucData['razon_social'] ?? null;

            if (!$razonSocial) {
                throw new Exception("No se pudo obtener la razón social de la empresa.", 400);
            }

            // Crear o validar la empresa con RUC y nombre real
            $empresa = $this->getEmpresaRepository()->getOrCreateByRucOrName($empresaRuc, $razonSocial);

            // Bloque 9: Validar/crear ModalidadTuc
            $modalidadNombre = ucwords(strtolower($vehiculoData['MODALIDAD'])); // Camioneta Rural
            $modalidadTuc = $this->getModalidadTucRepository()->getOrCreateByName($modalidadNombre);

            // Bloque 10: Validar/crear TipoServicio
            $tipoServicioNombre = ucwords(strtolower($vehiculoData['TIP_SERV'])); // Transporte Interurbano
            $tipoServicio = $this->getTipoServicioRepository()->getOrCreateByName($tipoServicioNombre);

            // Bloque 11: Validar/crear TipoTramite
            $tipoTramiteNombre = ucwords(strtolower($vehiculoData['TRAMITE'])); // Renovación De Concesión
            $tipoTramite = $this->getTipoTramiteRepository()->getOrCreateByName($tipoTramiteNombre);

            // Bloque 12: Validar estado TUC activo
            $estadoTuc = $this->getEstadoTucRepository()->getByNombre('Activo');

            // Bloque 13: Validar o crear Trámite TUC
            $tramiteTuc = $this->getTramiteTucRepository()->findByPlaca($dto->licensePlate);
            if (!$tramiteTuc) {
                $tramiteTuc = new TramiteTuc();
                $tramiteTuc->setCodigo('TR' . substr($dto->licensePlate, -4)); // TRM511

                $tramiteTuc->setFechaTramite(new DateTime($vehiculoData['FECHA_TRAM']));
                $tramiteTuc->setFechaEmision(new DateTime($vehiculoData['FECHA_EMI']));
                $tramiteTuc->setFechaCaducidad(new DateTime($vehiculoData['FECHA_CADUC']));

                $tramiteTuc->setEstado($estadoTuc);
                $tramiteTuc->setTipo($tipoTramite);
                $tramiteTuc->setModalidad($modalidadTuc);
                $tramiteTuc->setEmpresa($empresa);
                $tramiteTuc->setDistrito($distrito);
            }

            if (!$tramiteTuc->getEstado()->isActivo()) {
                throw new Exception("El trámite TUC asociado no está activo.", 400);
            }

            // Bloque 14: Crear Persona
            $persona = new Persona();
            $persona->setDocumento($dto->document);
            $persona->setTipoDocumento($tipoDocumento);

            $personaReniec = $reniecResponse['data'];
            if (isset($personaReniec['nombre_completo'])) {
                $nombresApellidos = $this->stringUtils->splitFullName($personaReniec['nombre_completo']);
                $persona->setNombre($nombresApellidos['nombres']);
                $persona->setApellido($nombresApellidos['apellidos']);
            } else if (isset($personaReniec['razon_social'])) {
                $persona->setNombre($personaReniec['razon_social']);
                $persona->setApellido('');
            }

            $persona->setActivo(true);
            $this->getPersonaRepository()->save($persona);

            // Bloque 15: Estado Activo
            $estadoActivo = $this->getEstadoUsuarioRepository()->getById(1);

            if (!$estadoActivo) {
                throw new Exception("No se encontró el estado 'Activo'.", 500);
            }

            // Bloque 16: Crear Usuario
            $usuario = new Usuario();
            $usuario->setAlias($dto->alias);
            $usuario->setClave(password_hash($dto->password, PASSWORD_DEFAULT));
            $usuario->setPersona($persona);
            $usuario->setEstado($estadoActivo);
            $this->getUsuarioRepository()->save($usuario);

            // Bloque 17: Perfil Conductor
            $perfilConductor = new PerfilConductor();
            $perfilConductor->setUsuario($usuario);
            $perfilConductor->setDisponible(false);
            $this->getPerfilConductorRepository()->save($perfilConductor);

            // Bloque 18: Registrar Contacto del usuario
            $contacto = new ContactoUsuario();
            $contacto->setUsuario($usuario);
            $contacto->setTipo($tipoContacto);
            $contacto->setValor($dto->contact);
            $contacto->setConfirmado(false);
            $contacto->setActivo(true);
            $this->getContactoUsuarioRepository()->save($contacto);

            // Bloque 19: Registrar Rol de Conductor
            $rolConductor = $this->getRolRepository()->getByNombre('Conductor');
            $rolUsuario = new RolUsuario();
            $rolUsuario->setUsuario($usuario);
            $rolUsuario->setRol($rolConductor);
            $rolUsuario->setActivo(true);
            $this->getRolUsuarioRepository()->save($rolUsuario);

            // Bloque 20: Crear Vehículo
            $vehiculo = new Vehiculo();
            $vehiculo->setPlaca($dto->licensePlate);
            $vehiculo->setModelo($modelo);
            $vehiculo->setColor($color);
            $vehiculo->setAnioFabricacion((int)$vehiculoData['ANIO_FABRIC']);
            $vehiculo->setNumeroAsientos((int)$vehiculoData['NUM_ASIENTOS']);
            $vehiculo->setNumeroPasajeros((int)$vehiculoData['NUM_PASAJ']);
            $vehiculo->setTipoCombustible($tipoCombustible);
            $vehiculo->setClase($clase);
            $vehiculo->setCategoria($categoria);
            $vehiculo->setActivo(true);
            $this->getVehiculoRepository()->save($vehiculo);

            // Bloque 21: Asociar Trámite TUC al Vehículo
            $tramiteTuc->setVehiculo($vehiculo);
            $this->getTramiteTucRepository()->save($tramiteTuc);

            // Bloque 22: Crear relación entre usuario y vehículo
            $vehiculoUsuario = new VehiculoUsuario();
            $vehiculoUsuario->setUsuario($usuario);
            $vehiculoUsuario->setVehiculo($vehiculo);
            $vehiculoUsuario->setActivo(true);
            $this->getVehiculoUsuarioRepository()->save($vehiculoUsuario);

            // Bloque 23: Registrar o validar Ruta de Servicio
            $rutaTexto = $vehiculoData['RUTA'] ?? null;
            if ($rutaTexto) {
                $rutaServicio = $this->getRutaServicioRepository()->findByTramite($tramiteTuc);

                if (!$rutaServicio) {
                    $rutaServicio = new RutaServicio();
                    $rutaServicio->setTramite($tramiteTuc);
                    $rutaServicio->setTipoServicio($tipoServicio);
                    $rutaServicio->setTexto($rutaTexto);
                    $rutaServicio->setActivo(true);

                    $this->getRutaServicioRepository()->save($rutaServicio, false);
                }
            }

            // Bloque 24: Crear especificación técnica del vehículo
            $especificacion = new EspecificacionTecnica();
            $especificacion->setVehiculo($vehiculo);
            $especificacion->setPesoSeco((float)str_replace(',', '.', $vehiculoData['PESO_SECO']));
            $especificacion->setPesoBruto((float)str_replace(',', '.', $vehiculoData['PESO_BRUTO']));
            $especificacion->setLongitud((float)str_replace(',', '.', $vehiculoData['LONGITUD']));
            $especificacion->setAltura((float)str_replace(',', '.', $vehiculoData['ALTURA']));
            $especificacion->setAnchura((float)str_replace(',', '.', $vehiculoData['ANCHURA']));
            $especificacion->setCargaUtil((float)str_replace(',', '.', $vehiculoData['CARGA_UTIL']));

            $this->getEspecificacionTecnicaRepository()->save($especificacion, false);

            // Bloque 25: Confirmar transacción
            $this->entityManager->commit();

            return [
                'message' => 'Registro exitoso',
                'userId' => $usuario->getId(),
                'personId' => $persona->getId(),
                'vehicleId' => $vehiculo->getId()
            ];

        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Solicita un código de recuperación para el usuario.
     *
     * @param RecoveryRequest $dto
     * @throws Exception
     */
    public function requestRecovery(RecoveryRequest $dto): void {
        // Buscar contacto
        $contacto = $this->getContactoUsuarioRepository()->findByTypeAndValue(
            $dto->contactTypeId,
            $dto->contact
        );

        if (!$contacto) {
            throw new Exception("Contacto no encontrado.", 404);
        }

        // Validar confirmación del contacto
        if (!$contacto->isConfirmado()) {
            throw new Exception("El contacto no está confirmado.", 400);
        }

        // Buscar usuario asociado
        $usuario = $this->getUsuarioRepository()->findOneByContact($contacto);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado.", 404);
        }

        // Obtener tipo "Recuperación"
        $tipoRecuperacion = $this->getTipoCodigoUsuarioRepository()->findOneByName('Recuperación');
        if (!$tipoRecuperacion) {
            throw new Exception("Tipo de código no encontrado.", 500);
        }

        // Generar código
        $codigo = $this->getCodigoUsuarioRepository()->generateRecoveryCode($usuario, $contacto, $tipoRecuperacion);

        // Seleccionar el servicio de notificación adecuado
        $service = $this->notificationServiceFactory->getServiceForContactType($dto->contactTypeId);
        $service->send($contacto->getValor(), 'Código de recuperación', $codigo->getCodigo());
    }

    /**
     * Verifica el código de recuperación.
     *
     * @param string $code
     * @param int $contactTypeId
     * @param string $contactValue
     * @return array
     * @throws Exception
     */
    public function verifyCode(string $code, int $contactTypeId, string $contactValue): array {
        // Buscar contacto
        $contacto = $this->getContactoUsuarioRepository()->findByTypeAndValue($contactTypeId, $contactValue);

        if (!$contacto) {
            throw new Exception("Contacto no encontrado.", 404);
        }

        // Buscar tipo "Recuperación"
        $tipoRecuperacion = $this->getTipoCodigoUsuarioRepository()->findOneByName('Recuperación');

        if (!$tipoRecuperacion) {
            throw new Exception("Tipo de código no encontrado.", 500);
        }

        // Buscar código válido
        $codigo = $this->getCodigoUsuarioRepository()->findValidCode($code, $contacto, $tipoRecuperacion->getId());

        if (!$codigo) {
            throw new Exception("Código inválido o expirado.", 400);
        }

        // Marcar como usado
        $this->getCodigoUsuarioRepository()->markAsUsed($codigo);

        return ['userId' => $codigo->getUsuario()->getId()];
    }

    /**
     * Restablece la contraseña del usuario.
     *
     * @param int $userId
     * @param string $newPassword
     * @throws Exception
     */
    public function resetPassword(int $userId, string $newPassword): void
    {
        // 1. Buscar usuario
        $usuario = $this->getUsuarioRepository()->find($userId);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado.", 404);
        }

        // 2. Obtener tipo "Recuperación"
        $tipoRecuperacion = $this->getTipoCodigoUsuarioRepository()->findOneByName('Recuperación');
        if (!$tipoRecuperacion) {
            throw new Exception("Tipo de código no encontrado.", 500);
        }

        // 3. Validar código de recuperación
        $codigoUsado = $this->getCodigoUsuarioRepository()->findValidRecoveryCodeByUserId(
            $userId,
            $tipoRecuperacion->getId()
        );

        if (!$codigoUsado) {
            throw new Exception("No se encontró un código válido para restablecer la contraseña.", 400);
        }

        // 4. Cambiar contraseña
        $usuario->setClave(password_hash($newPassword, PASSWORD_DEFAULT));
        $this->entityManager->flush();
    }
}