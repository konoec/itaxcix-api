<?php

namespace itaxcix\Core\UseCases\TucModality;

use itaxcix\Core\Domain\vehicle\TucModalityModel;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityResponseDTO;

class TucModalityUpdateUseCase
{
    private TucModalityRepositoryInterface $repository;

    public function __construct(TucModalityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, TucModalityRequestDTO $request): TucModalityResponseDTO
    {
        $tucModality = new TucModalityModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveTucModality($tucModality);

        return new TucModalityResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

