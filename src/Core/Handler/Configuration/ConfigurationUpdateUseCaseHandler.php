<?php

namespace itaxcix\Core\Handler\Configuration;

use itaxcix\Core\UseCases\Configuration\ConfigurationUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationRequestDTO;

class ConfigurationUpdateUseCaseHandler
{
    private ConfigurationUpdateUseCase $useCase;

    public function __construct(ConfigurationUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ConfigurationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}
