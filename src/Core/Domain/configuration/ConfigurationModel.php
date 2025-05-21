<?php

namespace itaxcix\Core\Domain\configuration;

class ConfigurationModel {
    private ?int $id = null;
    private string $key;
    private string $value;
    private bool $active = true;
}