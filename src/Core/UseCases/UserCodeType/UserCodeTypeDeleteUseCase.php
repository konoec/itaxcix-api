<?php

namespace itaxcix\Core\UseCases\UserCodeType;

use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;

class UserCodeTypeDeleteUseCase
{
    private UserCodeTypeRepositoryInterface $repository;
    private UserCodeRepositoryInterface $userCodeRepository;

    public function __construct(UserCodeTypeRepositoryInterface $repository, UserCodeRepositoryInterface $userCodeRepository)
    {
        $this->repository = $repository;
        $this->userCodeRepository = $userCodeRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the user code type is associated with any user codes
        $userCodes = $this->userCodeRepository->findActivesByUserCodeTypeId($id);
        if (!empty($userCodes)) {
            return false; // Cannot delete if there are associated user codes
        }

        return $this->repository->delete($id);
    }
}

