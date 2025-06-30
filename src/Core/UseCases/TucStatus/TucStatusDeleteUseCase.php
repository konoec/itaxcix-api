<?php

namespace itaxcix\Core\UseCases\TucStatus;

use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;

class TucStatusDeleteUseCase
{
    private TucStatusRepositoryInterface $repository;

    public function __construct(TucStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

