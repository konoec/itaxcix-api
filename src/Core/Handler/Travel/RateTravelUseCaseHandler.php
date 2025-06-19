<?php

namespace itaxcix\Core\Handler\Travel;

use InvalidArgumentException;
use itaxcix\Core\Domain\rating\RatingModel;
use itaxcix\Core\Interfaces\rating\RatingRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\UseCases\Travel\RateTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\RateTravelRequestDto;

class RateTravelUseCaseHandler implements RateTravelUseCase
{
    private RatingRepositoryInterface $ratingRepository;
    private TravelRepositoryInterface $travelRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;

    public function __construct(
        RatingRepositoryInterface $ratingRepository,
        TravelRepositoryInterface $travelRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository
    ) {
        $this->ratingRepository = $ratingRepository;
        $this->travelRepository = $travelRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
    }

    public function execute(RateTravelRequestDto $dto): void
    {
        // Validar que el viaje exista
        $travel = $this->travelRepository->findTravelById($dto->travelId);
        if (!$travel) {
            throw new InvalidArgumentException('El viaje no existe');
        }

        // Validar que el usuario no haya calificado ya este viaje
        $existingRating = $this->ratingRepository->findRatingByTravelIdAndRaterId($dto->travelId, $dto->raterId);
        if ($existingRating) {
            throw new InvalidArgumentException('No puedes calificar dos veces el mismo viaje');
        }

        // Determinar si el calificador es conductor o ciudadano
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->raterId);
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($dto->raterId);

        if ($driverProfile) {
            // Califica el conductor al ciudadano
            $ratedUserId = $travel->getCitizen()->getId();
            $ratedProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($ratedUserId);
            if (!$ratedProfile) {
                throw new InvalidArgumentException('Perfil de ciudadano no encontrado');
            }
        } elseif ($citizenProfile) {
            // Califica el ciudadano al conductor
            $ratedUserId = $travel->getDriver()->getId();
            $ratedProfile = $this->driverProfileRepository->findDriverProfileByUserId($ratedUserId);
            if (!$ratedProfile) {
                throw new InvalidArgumentException('Perfil de conductor no encontrado');
            }


        } else {
            throw new InvalidArgumentException('El usuario calificador no es ni conductor ni ciudadano');
        }

        if($dto->raterId === $travel->getCitizen()->getId()){
            $rated = $travel->getDriver();
            $rater = $travel->getCitizen();
        }else{
            $rated = $travel->getCitizen();
            $rater = $travel->getDriver();
        }

        $rating = new RatingModel(
            0,
            $rater,
            $rated,
            $travel,
            $dto->score,
            $dto->comment
        );

        // Guardar la calificación
        $this->ratingRepository->saveRating(
            $rating
        );

        // Actualizar promedio y conteo de calificaciones de forma incremental
        $currentCount = $ratedProfile->getRatingCount();
        $currentAvg = $ratedProfile->getAverageRating();
        $newCount = $currentCount + 1;
        $newAvg = ($currentAvg * $currentCount + $dto->score) / $newCount;
        $ratedProfile->setAverageRating($newAvg);
        $ratedProfile->setRatingCount($newCount);

        // Guardar el perfil actualizado
        if ($driverProfile) {
            $this->citizenProfileRepository->saveCitizenProfile($ratedProfile);
        } else {
            $this->driverProfileRepository->saveDriverProfile($ratedProfile);
        }
    }
}
