<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TechnicalSpecificationModel;

interface TechnicalSpecificationRepositoryInterface
{
    public function saveTechnicalSpecification(TechnicalSpecificationModel $technicalSpecificationModel): TechnicalSpecificationModel;
}