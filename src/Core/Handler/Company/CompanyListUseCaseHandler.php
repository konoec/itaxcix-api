<?php

namespace itaxcix\Core\Handler\Company;

use itaxcix\Core\UseCases\Company\CompanyListUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;

class CompanyListUseCaseHandler
{
    private CompanyListUseCase $useCase;

    public function __construct(CompanyListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(CompanyPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
