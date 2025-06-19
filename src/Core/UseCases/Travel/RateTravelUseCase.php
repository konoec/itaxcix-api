<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\RateTravelRequestDto;

interface RateTravelUseCase
{
    public function execute(RateTravelRequestDto $dto): void;
}

