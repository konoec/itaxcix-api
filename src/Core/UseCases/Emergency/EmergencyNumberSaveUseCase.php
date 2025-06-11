<?php

namespace itaxcix\Core\UseCases\Emergency;

interface EmergencyNumberSaveUseCase
{
    /**
     * @param string $number
     * @return bool
     */
    public function execute(string $number): bool;
}

