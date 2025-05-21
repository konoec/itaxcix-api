<?php

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRepository;
use function DI\autowire;

return [
    UserRepositoryInterface::class => autowire(DoctrineUserRepository::class),
];