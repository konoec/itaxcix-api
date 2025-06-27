<?php

namespace itaxcix\Core\Handler\Travel;

use InvalidArgumentException;
use itaxcix\Core\Domain\rating\RatingModel;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\UseCases\Travel\GetTravelRatingsByTravelUseCase;
use itaxcix\Core\Interfaces\rating\RatingRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Travel\TravelRatingItemDto;
use itaxcix\Shared\DTO\useCases\Travel\TravelRatingsByTravelResponseDto;

class GetTravelRatingsByTravelUseCaseHandler implements GetTravelRatingsByTravelUseCase
{
    private RatingRepositoryInterface $ratingRepository;
    private TravelRepositoryInterface $travelRepository;

    public function __construct(RatingRepositoryInterface $ratingRepository, TravelRepositoryInterface $travelRepository)
    {
        $this->ratingRepository = $ratingRepository;
        $this->travelRepository = $travelRepository;
    }

    public function execute(int $travelId): TravelRatingsByTravelResponseDto
    {
        $ratings = $this->ratingRepository->findRatingsByTravelId($travelId);
        $travel = $this->travelRepository->findTravelById($travelId);

        if (!$travel) {
            throw new InvalidArgumentException("Travel with ID $travelId not found.");
        }

        $driverId = $travel->getDriver()->getId();
        $citizenId = $travel->getCitizen()->getId();
        $driverRating = null;
        $citizenRating = null;

        foreach ($ratings as $rating) {
            if (!$rating instanceof RatingModel) {
                continue; // o lanza una excepción si es crítico
            }

            if ($rating->getRater()->getId() === $driverId) {
                $driverRating = new TravelRatingItemDto(
                    id: $rating->getId(),
                    travelId: $rating->getTravel()->getId(),
                    raterName: $rating->getRater()->getPerson()->getName(),
                    ratedName: $rating->getRated()->getPerson()->getName(),
                    score: $rating->getScore(),
                    comment: $rating->getComment(),
                    createdAt: ""
                );
            } elseif ($rating->getRater()->getId() === $citizenId) {
                $citizenRating = new TravelRatingItemDto(
                    id: $rating->getId(),
                    travelId: $rating->getTravel()->getId(),
                    raterName: $rating->getRater()->getPerson()->getName(),
                    ratedName: $rating->getRated()->getPerson()->getName(),
                    score: $rating->getScore(),
                    comment: $rating->getComment(),
                    createdAt: ""
                );
            }
        }
        return new TravelRatingsByTravelResponseDto(
            driverRating: $driverRating,
            citizenRating: $citizenRating
        );
    }
}
