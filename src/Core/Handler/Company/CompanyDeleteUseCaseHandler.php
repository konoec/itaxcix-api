<?php

namespace itaxcix\Core\Handler\Company;

use itaxcix\Core\UseCases\Company\CompanyDeleteUseCase;

class CompanyDeleteUseCaseHandler
{
    private CompanyDeleteUseCase $useCase;

    public function __construct(CompanyDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
