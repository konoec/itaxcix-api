<?php

namespace itaxcix\Core\Handler\Configuration;

use itaxcix\Core\UseCases\Configuration\ConfigurationDeleteUseCase;

class ConfigurationDeleteUseCaseHandler
{
    private ConfigurationDeleteUseCase $useCase;

    public function __construct(ConfigurationDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): array
    {
        return $this->useCase->execute($id);
    }
}
