<?php

namespace itaxcix\Core\UseCases\HelpCenter;

use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterRequestDTO;

interface HelpCenterUpdateUseCase
{
    /**
     * @param HelpCenterRequestDTO $dto
     * @return array
     */
    public function execute(HelpCenterRequestDTO $dto): array;
}
