<?php

namespace itaxcix\Core\Interfaces\help;

use itaxcix\Core\Domain\help\HelpCenterModel;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

interface HelpCenterRepositoryInterface
{
    public function findAllActiveHelpItems(): array;
    public function findAllHelpItems(): array;
    public function findAllHelpItemsPaginated(int $page, int $perPage): PaginationResponseDTO;
    public function findHelpItemById(int $id): ?HelpCenterModel;
    public function saveHelpItem(HelpCenterModel $helpItem): HelpCenterModel;
    public function deleteHelpItem(int $id): bool;
}
