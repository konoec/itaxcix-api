<?php

namespace itaxcix\Core\Handler\InfractionStatus;

use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusListUseCase;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusPaginationRequestDTO;

class InfractionStatusListUseCaseHandler
{
    private InfractionStatusListUseCase $useCase;

    public function __construct(InfractionStatusListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(InfractionStatusPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

