<?php

namespace itaxcix\Core\UseCases\TucModality;

use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;

class TucModalityDeleteUseCase
{
    private TucModalityRepositoryInterface $repository;

    public function __construct(TucModalityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

