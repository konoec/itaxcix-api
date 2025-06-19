<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\TravelHistoryResponseDto;

interface GetTravelHistoryUseCase
{
    public function execute(int $userId, int $page, int $perPage): TravelHistoryResponseDto;
}

