<?php

use itaxcix\Infrastructure\Database\EventListener\AuditEventListener;
use function DI\autowire;

return [
    AuditEventListener::class => autowire(),
];
