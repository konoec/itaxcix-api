<?php

namespace itaxcix\Core\UseCases\Incident;

use itaxcix\Shared\DTO\useCases\Incident\RegisterIncidentRequestDTO;

interface RegisterIncidentUseCase
{
    public function execute(RegisterIncidentRequestDTO $dto): array;
}

