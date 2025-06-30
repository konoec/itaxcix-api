<?php

namespace itaxcix\Core\Interfaces\rating;

use itaxcix\Core\Domain\rating\RatingModel;

interface RatingRepositoryInterface
{
    public function saveRating(RatingModel $ratingModel): RatingModel;

    public function findRatingByTravelIdAndRaterId(int $travelId, int $raterId): ?RatingModel;
    public function findRatingsByTravelId(int $travelId): array;
    public function findRatingsReceivedByUserId(int $userId, int $offset, int $limit): array;
    public function countRatingsReceivedByUserId(int $userId): int;
    public function getAverageRatingForUser(int $userId): float;

    // Métodos para reporte administrativo de calificaciones
    public function findReport(\itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO $dto): array;
    public function countReport(\itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO $dto): int;
}