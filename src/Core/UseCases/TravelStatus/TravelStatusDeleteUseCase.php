<?php

namespace itaxcix\Core\UseCases\TravelStatus;

use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;

class TravelStatusDeleteUseCase
{
    private TravelStatusRepositoryInterface $repository;

    public function __construct(TravelStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

