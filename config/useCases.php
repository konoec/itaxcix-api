<?php

use itaxcix\Core\Handler\BiometricValidationUseCaseHandler;
use itaxcix\Core\Handler\ChangePasswordUseCaseHandler;
use itaxcix\Core\Handler\DocumentValidationUseCaseHandler;
use itaxcix\Core\Handler\LoginUseCaseHandler;
use itaxcix\Core\Handler\ResendVerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\StartPasswordRecoveryUseCaseHandler;
use itaxcix\Core\Handler\UserRegistrationUseCaseHandler;
use itaxcix\Core\Handler\VehicleValidationValidatorUseCaseHandler;
use itaxcix\Core\Handler\VerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\VerifyRecoveryCodeUseCaseHandler;
use itaxcix\Core\UseCases\BiometricValidationUseCase;
use itaxcix\Core\UseCases\ChangePasswordUseCase;
use itaxcix\Core\UseCases\DocumentValidationUseCase;
use itaxcix\Core\UseCases\LoginUseCase;
use itaxcix\Core\UseCases\ResendVerificationCodeUseCase;
use itaxcix\Core\UseCases\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\UserRegistrationUseCase;
use itaxcix\Core\UseCases\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\VerificationCodeUseCase;
use itaxcix\Core\UseCases\VerifyRecoveryCodeUseCase;
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
    ResendVerificationCodeUseCase::class => autowire(ResendVerificationCodeUseCaseHandler::class),
];