<?php

namespace itaxcix\Core\UseCases\Vehicle;

interface DisassociateUserVehicleUseCase
{
    public function execute(int $userId): bool;
}
