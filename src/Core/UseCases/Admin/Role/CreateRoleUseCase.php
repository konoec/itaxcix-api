<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\CreateRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\RoleResponseDTO;
use InvalidArgumentException;

class CreateRoleUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(CreateRoleRequestDTO $request): RoleResponseDTO
    {
        // Verificar que no exista un rol con el mismo nombre
        $existingRole = $this->roleRepository->findRoleByName($request->name);
        if ($existingRole !== null) {
            throw new InvalidArgumentException('Ya existe un rol con ese nombre');
        }

        $role = new RoleModel(
            id: null,
            name: $request->name,
            active: $request->active ?? true,
            web: $request->web ?? false
        );

        $savedRole = $this->roleRepository->save($role);

        return new RoleResponseDTO(
            id: $savedRole->getId(),
            name: $savedRole->getName(),
            active: $savedRole->isActive(),
            web: $savedRole->isWeb()
        );
    }
}
