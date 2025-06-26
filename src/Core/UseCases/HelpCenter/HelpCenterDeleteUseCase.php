<?php

namespace itaxcix\Core\UseCases\HelpCenter;

interface HelpCenterDeleteUseCase
{
    /**
     * @param int $id
     * @return array
     */
    public function execute(int $id): array;
}
