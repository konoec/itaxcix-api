<?php

namespace itaxcix\Core\UseCases\Admission;

use itaxcix\Shared\DTO\useCases\Admission\ApproveDriverRequestDto;

interface ApproveDriverAdmissionUseCase
{
    public function execute(ApproveDriverRequestDto $dto): ?array;
}