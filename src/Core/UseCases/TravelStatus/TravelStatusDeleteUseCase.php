<?php

namespace itaxcix\Core\UseCases\TravelStatus;

use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;

class TravelStatusDeleteUseCase
{
    private TravelStatusRepositoryInterface $repository;
    private TravelRepositoryInterface $travelRepository;

    public function __construct(TravelStatusRepositoryInterface $repository, TravelRepositoryInterface $travelRepository)
    {
        $this->repository = $repository;
        $this->travelRepository = $travelRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the travel status is associated with any travels
        $travels = $this->travelRepository->findActivesByTravelStatusId($id);
        if (!empty($travels)) {
            return false; // Cannot delete if there are associated travels
        }

        return $this->repository->delete($id);
    }
}

