<?php

namespace itaxcix\Core\Interfaces\rating;

use itaxcix\Core\Domain\rating\RatingModel;
use itaxcix\Core\Domain\travel\TravelModel;

interface RatingRepositoryInterface
{
    public function saveRating(RatingModel $ratingModel): RatingModel;

    public function findRatingByTravelIdAndRaterId(int $travelId, int $raterId): ?RatingModel;
    public function findRatingsByTravelId(int $travelId): array;
}