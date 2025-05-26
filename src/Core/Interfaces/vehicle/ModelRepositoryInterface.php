<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ModelModel;

interface ModelRepositoryInterface
{
    public function findAllModelByName(string $name): ?ModelModel;
    public function saveModel(ModelModel $modelModel): ModelModel;
}