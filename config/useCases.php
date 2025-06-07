<?php

use itaxcix\Core\Handler\Admission\ApproveDriverAdmissionUseCaseHandler;
use itaxcix\Core\Handler\Admission\GetDriverDetailsUseCaseHandler;
use itaxcix\Core\Handler\Admission\GetPendingDriversUseCaseHandler;
use itaxcix\Core\Handler\Admission\RejectDriverAdmissionUseCaseHandler;
use itaxcix\Core\Handler\Auth\BiometricValidationUseCaseHandler;
use itaxcix\Core\Handler\Auth\ChangePasswordUseCaseHandler;
use itaxcix\Core\Handler\Auth\DocumentValidationUseCaseHandler;
use itaxcix\Core\Handler\Auth\LoginUseCaseHandler;
use itaxcix\Core\Handler\Auth\ResendVerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\Auth\StartPasswordRecoveryUseCaseHandler;
use itaxcix\Core\Handler\Auth\UserRegistrationUseCaseHandler;
use itaxcix\Core\Handler\Auth\VehicleValidationValidatorUseCaseHandler;
use itaxcix\Core\Handler\Auth\VerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\Auth\VerifyRecoveryCodeUseCaseHandler;
use itaxcix\Core\Handler\Driver\DriverTucStatusUseCaseHandler;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Core\UseCases\Admission\RejectDriverAdmissionUseCase;
use itaxcix\Core\UseCases\Auth\BiometricValidationUseCase;
use itaxcix\Core\UseCases\Auth\ChangePasswordUseCase;
use itaxcix\Core\UseCases\Auth\DocumentValidationUseCase;
use itaxcix\Core\UseCases\Auth\LoginUseCase;
use itaxcix\Core\UseCases\Auth\ResendVerificationCodeUseCase;
use itaxcix\Core\UseCases\Auth\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\Auth\UserRegistrationUseCase;
use itaxcix\Core\UseCases\Auth\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\Auth\VerificationCodeUseCase;
use itaxcix\Core\UseCases\Auth\VerifyRecoveryCodeUseCase;
use itaxcix\Core\UseCases\Driver\DriverTucStatusUseCase;
use function DI\autowire;

return [
    // Auth Use Cases
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

    // Admission Use Cases
    ApproveDriverAdmissionUseCase::class => autowire(ApproveDriverAdmissionUseCaseHandler::class),
    GetDriverDetailsUseCase::class => autowire(GetDriverDetailsUseCaseHandler::class),
    GetPendingDriversUseCase::class => autowire(GetPendingDriversUseCaseHandler::class),
    RejectDriverAdmissionUseCase::class => autowire(RejectDriverAdmissionUseCaseHandler::class),

    // Driver Use Cases
    DriverTucStatusUseCase::class => autowire(DriverTucStatusUseCaseHandler::class),
];