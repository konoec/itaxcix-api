<?php

namespace itaxcix\Core\UseCases\Admission;

interface GetPendingDriversUseCase
{
    public function execute(): ?array;
}