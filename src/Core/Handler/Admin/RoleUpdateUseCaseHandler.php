<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RoleUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RoleUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;

class RoleUpdateUseCaseHandler implements RoleUpdateUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(RoleUpdateRequestDTO $dto): ?RoleResponseDTO
    {
        // Buscar el rol a actualizar
        $role = $this->roleRepository->findRoleById($dto->id);
        if (!$role) {
            throw new InvalidArgumentException('El rol no existe.');
        }

        // Verificar si el nuevo nombre ya existe en otro rol
        $existingRole = $this->roleRepository->findRoleByName($dto->name);
        if ($existingRole && $existingRole->getId() !== $dto->id) {
            throw new InvalidArgumentException('Ya existe otro rol con ese nombre.');
        }

        // Actualizar los datos del rol
        $role->setName($dto->name);
        $role->setActive($dto->active);
        $role->setWeb($dto->web);

        // Guardar los cambios
        $updatedRole = $this->roleRepository->saveRole($role);

        // Crear y retornar el DTO de respuesta
        return new RoleResponseDTO(
            id: $updatedRole->getId(),
            name: $updatedRole->getName(),
            active: $updatedRole->isActive(),
            web: $updatedRole->isWeb()
        );
    }
}
