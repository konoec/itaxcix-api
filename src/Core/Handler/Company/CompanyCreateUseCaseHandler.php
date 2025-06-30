<?php

namespace itaxcix\Core\Handler\Company;

use itaxcix\Core\UseCases\Company\CompanyCreateUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyCreateUseCaseHandler
{
    private CompanyCreateUseCase $useCase;

    public function __construct(CompanyCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(CompanyRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
