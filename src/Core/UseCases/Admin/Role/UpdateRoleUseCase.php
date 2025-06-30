<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\UpdateRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\RoleResponseDTO;
use InvalidArgumentException;

class UpdateRoleUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(UpdateRoleRequestDTO $request): RoleResponseDTO
    {
        // Verificar que el rol existe
        $role = $this->roleRepository->findById($request->id);
        if ($role === null) {
            throw new InvalidArgumentException('Rol no encontrado');
        }

        // Verificar que no exista otro rol con el mismo nombre
        $existingRole = $this->roleRepository->findRoleByName($request->name);
        if ($existingRole !== null && $existingRole->getId() !== $request->id) {
            throw new InvalidArgumentException('Ya existe otro rol con ese nombre');
        }

        // Actualizar el rol
        $role->setName($request->name);
        $role->setActive($request->active);
        $role->setWeb($request->web);

        $savedRole = $this->roleRepository->save($role);

        return new RoleResponseDTO(
            id: $savedRole->getId(),
            name: $savedRole->getName(),
            active: $savedRole->isActive(),
            web: $savedRole->isWeb()
        );
    }
}
