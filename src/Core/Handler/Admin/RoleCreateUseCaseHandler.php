<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RoleCreateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;

class RoleCreateUseCaseHandler implements RoleCreateUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(RoleCreateRequestDTO $dto): ?RoleResponseDTO
    {
        // Verificar si ya existe un rol con el mismo nombre
        $existingRole = $this->roleRepository->findRoleByName($dto->name);
        if ($existingRole) {
            throw new InvalidArgumentException('Ya existe un rol con ese nombre.');
        }

        // Crear nuevo rol
        $newRole = new RoleModel(
            id: null,
            name: $dto->name,
            active: $dto->active,
            web: $dto->web
        );

        // Guardar el rol
        $savedRole = $this->roleRepository->saveRole($newRole);

        // Crear y retornar el DTO de respuesta
        return new RoleResponseDTO(
            id: $savedRole->getId(),
            name: $savedRole->getName(),
            active: $savedRole->isActive(),
            web: $savedRole->isWeb()
        );
    }
}
