<?php

namespace itaxcix\Core\Handler\Emergency;

use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberGetUseCase;

class EmergencyNumberGetUseCaseHandler implements EmergencyNumberGetUseCase
{
    private ConfigurationRepositoryInterface $configurationRepository;
    private const EMERGENCY_KEY = 'ITAXCIX_NUMERO_EMERGENCIA';

    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function execute(): ?string
    {
        $config = $this->configurationRepository->findConfigurationByKey(self::EMERGENCY_KEY);
        return $config?->getValue();
    }
}
