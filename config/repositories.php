<?php

use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Repository\person\DoctrineDocumentTypeRepository;
use itaxcix\Infrastructure\Database\Repository\person\DoctrinePersonRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineRolePermissionRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRoleRepository;
use function DI\autowire;

return [
    UserRepositoryInterface::class => autowire(DoctrineUserRepository::class),
    RolePermissionRepositoryInterface::class => autowire(DoctrineRolePermissionRepository::class),
    UserRoleRepositoryInterface::class => autowire(DoctrineUserRoleRepository::class),
    PersonRepositoryInterface::class => autowire(DoctrinePersonRepository::class),
    DocumentTypeRepositoryInterface::class => autowire(DoctrineDocumentTypeRepository::class)
];