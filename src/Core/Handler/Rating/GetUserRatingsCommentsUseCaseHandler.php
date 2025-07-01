<?php

namespace itaxcix\Core\Handler\Rating;

use itaxcix\Core\Interfaces\rating\RatingRepositoryInterface;
use itaxcix\Core\UseCases\Rating\GetUserRatingsCommentsUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\useCases\Rating\UserRatingCommentDto;
use itaxcix\Shared\DTO\useCases\Rating\UserRatingsResponseDto;

class GetUserRatingsCommentsUseCaseHandler implements GetUserRatingsCommentsUseCase
{
    private RatingRepositoryInterface $ratingRepository;

    public function __construct(RatingRepositoryInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    public function execute(int $userId, int $page, int $perPage): UserRatingsResponseDto
    {
        // Calcular offset para la paginación
        $offset = ($page - 1) * $perPage;

        // Obtener calificaciones paginadas
        $ratings = $this->ratingRepository->findRatingsReceivedByUserId($userId, $offset, $perPage);

        // Obtener el total de calificaciones y promedio
        $totalRatings = $this->ratingRepository->countRatingsReceivedByUserId($userId);
        $averageRating = $this->ratingRepository->getAverageRatingForUser($userId);

        // Mapear las calificaciones a DTOs de comentarios
        $comments = array_map(function ($rating) {
            $raterPerson = $rating->getRater()->getPerson();
            $fullName = trim(($raterPerson->getName() ?? '') . ' ' . ($raterPerson->getLastName() ?? ''));

            return new UserRatingCommentDto(
                id: $rating->getId(),
                travelId: $rating->getTravel()->getId(),
                raterName: $fullName ?: 'Usuario',
                score: $rating->getScore(),
                comment: $rating->getComment(),
                createdAt: $rating->getTravel()->getCreationDate()->format('c')
            );
        }, $ratings);

        // Calcular metadatos de paginación
        $lastPage = (int) ceil($totalRatings / $perPage);
        $meta = new PaginationMetaDTO(
            total: $totalRatings,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        return new UserRatingsResponseDto(
            averageRating: round($averageRating, 2),
            totalRatings: $totalRatings,
            comments: $comments,
            meta: $meta
        );
    }
}
