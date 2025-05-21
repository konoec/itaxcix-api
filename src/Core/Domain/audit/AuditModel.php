<?php

namespace itaxcix\Core\Domain\audit;

use DateTime;

class AuditModel {
    private ?int $id = null;
    private string $affectedTable;
    private string $operation;
    private string $systemUser;
    private DateTime $date;
    private ?array $previousData = null;
    private ?array $newData = null;
}