<?php

namespace itaxcix\Core\Interfaces\person;

use itaxcix\Core\Domain\person\DocumentTypeModel;

interface DocumentTypeRepositoryInterface
{
    public function findDocumentTypeByName(string $name): ?DocumentTypeModel;
}