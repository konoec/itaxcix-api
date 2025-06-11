<?php

namespace itaxcix\Core\Handler\Emergency;

use itaxcix\Core\Domain\configuration\ConfigurationModel;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberSaveUseCase;

class EmergencyNumberSaveUseCaseHandler implements EmergencyNumberSaveUseCase
{
    private ConfigurationRepositoryInterface $configurationRepository;
    private const EMERGENCY_KEY = 'ITAXCIX_NUMERO_EMERGENCIA';

    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function execute(string $number): bool
    {
        $config = $this->configurationRepository->findConfigurationByKey(self::EMERGENCY_KEY);
        if ($config) {
            $config->setValue($number);
        } else {
            $config = new ConfigurationModel(null, self::EMERGENCY_KEY, $number, true);
        }
        $this->configurationRepository->saveConfiguration($config);
        return true;
    }
}
