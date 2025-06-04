<?php

namespace itaxcix\Core\UseCases\Admission;

use itaxcix\Shared\DTO\useCases\Admission\RejectDriverRequestDto;

interface RejectDriverAdmissionUseCase
{
    public function execute(RejectDriverRequestDto $dto): ?array;
}