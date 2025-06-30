<?php

namespace itaxcix\Core\Handler\Company;

use itaxcix\Core\UseCases\Company\CompanyUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyUpdateUseCaseHandler
{
    private CompanyUpdateUseCase $useCase;

    public function __construct(CompanyUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, CompanyRequestDTO $request): array
    {
        return $this->useCase->execute($id, $request);
    }
}
