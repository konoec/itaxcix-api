<?php

namespace itaxcix\Core\Handler\Configuration;

use itaxcix\Core\UseCases\Configuration\ConfigurationListUseCase;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationPaginationRequestDTO;

class ConfigurationListUseCaseHandler
{
    private ConfigurationListUseCase $useCase;

    public function __construct(ConfigurationListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ConfigurationPaginationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}
