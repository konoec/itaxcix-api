<?php

namespace itaxcix\Core\UseCases\UserCodeType;

use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;

class UserCodeTypeDeleteUseCase
{
    private UserCodeTypeRepositoryInterface $repository;

    public function __construct(UserCodeTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

