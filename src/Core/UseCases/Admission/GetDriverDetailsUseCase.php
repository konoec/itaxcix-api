<?php

namespace itaxcix\Core\UseCases\Admission;

interface GetDriverDetailsUseCase
{
    public function execute(int $driverId): ?array;
}