<?php

namespace itaxcix\Core\UseCases\Rating;

use itaxcix\Shared\DTO\useCases\Rating\UserRatingsResponseDto;

interface GetUserRatingsCommentsUseCase
{
    public function execute(int $userId, int $page, int $perPage): UserRatingsResponseDto;
}
