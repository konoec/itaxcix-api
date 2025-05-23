<?php

use itaxcix\Core\UseCases\BiometricValidationUseCase;
use itaxcix\Core\UseCases\BiometricValidationUseCaseHandler;
use itaxcix\Core\UseCases\DocumentValidationUseCase;
use itaxcix\Core\UseCases\DocumentValidationUseCaseHandler;
use itaxcix\Core\UseCases\LoginUseCase;
use itaxcix\Core\UseCases\LoginUseCaseHandler;
use itaxcix\Core\UseCases\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\VehicleValidationValidatorUseCaseHandler;
use function DI\autowire;

return [
    LoginUseCase::class => autowire(LoginUseCaseHandler::class),
    DocumentValidationUseCase::class => autowire(DocumentValidationUseCaseHandler::class),
    VehicleValidationValidatorUseCase::class => autowire(VehicleValidationValidatorUseCaseHandler::class),
    BiometricValidationUseCase::class => autowire(BiometricValidationUseCaseHandler::class),
];