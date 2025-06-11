<?php

namespace itaxcix\Core\UseCases\Emergency;

interface EmergencyNumberGetUseCase
{
    /**
     * @return string|null
     */
    public function execute(): ?string;
}

