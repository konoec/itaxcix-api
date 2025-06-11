<?php

namespace itaxcix\Core\Interfaces\configuration;

use itaxcix\Core\Domain\configuration\ConfigurationModel;

interface ConfigurationRepositoryInterface
{
    public function findConfigurationByKey(string $key): ?ConfigurationModel;
    public function saveConfiguration(ConfigurationModel $configurationModel): ConfigurationModel;
}

