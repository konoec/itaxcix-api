<?php

use itaxcix\Core\UseCases\BiometricValidationUseCase;
use itaxcix\Core\UseCases\BiometricValidationUseCaseHandler;
use itaxcix\Core\UseCases\ChangePasswordUseCase;
use itaxcix\Core\UseCases\ChangePasswordUseCaseHandler;
use itaxcix\Core\UseCases\DocumentValidationUseCase;
use itaxcix\Core\UseCases\DocumentValidationUseCaseHandler;
use itaxcix\Core\UseCases\LoginUseCase;
use itaxcix\Core\UseCases\LoginUseCaseHandler;
use itaxcix\Core\UseCases\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\StartPasswordRecoveryUseCaseHandler;
use itaxcix\Core\UseCases\UserRegistrationUseCase;
use itaxcix\Core\UseCases\UserRegistrationUseCaseHandler;
use itaxcix\Core\UseCases\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\VehicleValidationValidatorUseCaseHandler;
use itaxcix\Core\UseCases\VerificationCodeUseCase;
use itaxcix\Core\UseCases\VerificationCodeUseCaseHandler;
use itaxcix\Core\UseCases\VerifyRecoveryCodeUseCase;
use itaxcix\Core\UseCases\VerifyRecoveryCodeUseCaseHandler;
use function DI\autowire;

return [
    LoginUseCase::class => autowire(LoginUseCaseHandler::class),
    DocumentValidationUseCase::class => autowire(DocumentValidationUseCaseHandler::class),
    VehicleValidationValidatorUseCase::class => autowire(VehicleValidationValidatorUseCaseHandler::class),
    BiometricValidationUseCase::class => autowire(BiometricValidationUseCaseHandler::class),
    UserRegistrationUseCase::class => autowire(UserRegistrationUseCaseHandler::class),
    VerificationCodeUseCase::class => autowire(VerificationCodeUseCaseHandler::class),
    ChangePasswordUseCase::class => autowire(ChangePasswordUseCaseHandler::class),
    StartPasswordRecoveryUseCase::class => autowire(StartPasswordRecoveryUseCaseHandler::class),
    VerifyRecoveryCodeUseCase::class => autowire(VerifyRecoveryCodeUseCaseHandler::class),
];