<?php

namespace itaxcix\Core\Handler\Configuration;

use itaxcix\Core\UseCases\Configuration\ConfigurationCreateUseCase;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationRequestDTO;

class ConfigurationCreateUseCaseHandler
{
    private ConfigurationCreateUseCase $useCase;

    public function __construct(ConfigurationCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ConfigurationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}
