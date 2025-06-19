<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\UseCases\Travel\GetTravelHistoryUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelHistoryItemDto;
use itaxcix\Shared\DTO\useCases\Travel\TravelHistoryResponseDto;

class GetTravelHistoryUseCaseHandler implements GetTravelHistoryUseCase
{
    private TravelRepositoryInterface $travelRepository;

    public function __construct(TravelRepositoryInterface $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function execute(int $userId, int $page, int $perPage): TravelHistoryResponseDto
    {
        $offset = ($page - 1) * $perPage;
        $travels = $this->travelRepository->findTravelsByUserId($userId, $offset, $perPage);
        $total = $this->travelRepository->countTravelsByUserId($userId);

        $items = array_map(function ($travel) {
            return new TravelHistoryItemDto(
                id: $travel->getId(),
                startDate: $travel->getStartDate() ? $travel->getStartDate()->format('c') : '',
                origin: $travel->getOrigin()->getName(),
                destination: $travel->getDestination()->getName(),
                status: $travel->getStatus()->getName()
            );
        }, $travels);

        $lastPage = (int) ceil($total / $perPage);
        $meta = new PaginationMetaDTO(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        return new TravelHistoryResponseDto(
            items: $items,
            meta: $meta
        );
    }
}
