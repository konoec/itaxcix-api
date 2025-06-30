<?php

namespace itaxcix\Core\UseCases\Model;

use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;

class ModelDeleteUseCase
{
    private ModelRepositoryInterface $modelRepository;

    public function __construct(ModelRepositoryInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }

    public function execute(int $id): bool
    {
        $existingModel = $this->modelRepository->findById($id);
        if (!$existingModel) {
            throw new \InvalidArgumentException('El modelo especificado no existe');
        }

        return $this->modelRepository->delete($id);
    }
}
