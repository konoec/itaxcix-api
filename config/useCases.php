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
use itaxcix\Core\Handler\Emergency\EmergencyNumberGetUseCaseHandler;
use itaxcix\Core\Handler\Emergency\EmergencyNumberSaveUseCaseHandler;
use itaxcix\Core\Handler\Incident\RegisterIncidentUseCaseHandler;
use itaxcix\Core\Handler\Travel\CancelTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\CompleteTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\GetTravelHistoryUseCaseHandler;
use itaxcix\Core\Handler\Travel\GetTravelRatingsByTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RateTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RequestNewTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RespondToTravelRequestUseCaseHandler;
use itaxcix\Core\Handler\Travel\StartAcceptedTravelUseCaseHandler;
use itaxcix\Core\Handler\User\UserProfilePhotoUploadUseCaseHandler;
use itaxcix\Core\Handler\User\UserProfilePhotoUseCaseHandler;
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
use itaxcix\Core\UseCases\Emergency\EmergencyNumberGetUseCase;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberSaveUseCase;
use itaxcix\Core\UseCases\Incident\RegisterIncidentUseCase;
use itaxcix\Core\UseCases\Travel\CancelTravelUseCase;
use itaxcix\Core\UseCases\Travel\CompleteTravelUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelHistoryUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelRatingsByTravelUseCase;
use itaxcix\Core\UseCases\Travel\RateTravelUseCase;
use itaxcix\Core\UseCases\Travel\RequestNewTravelUseCase;
use itaxcix\Core\UseCases\Travel\RespondToTravelRequestUseCase;
use itaxcix\Core\UseCases\Travel\StartAcceptedTravelUseCase;
use itaxcix\Core\UseCases\User\UserProfilePhotoUploadUseCase;
use itaxcix\Core\UseCases\User\UserProfilePhotoUseCase;
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

    // User Use Cases
    UserProfilePhotoUploadUseCase::class => autowire(UserProfilePhotoUploadUseCaseHandler::class),
    UserProfilePhotoUseCase::class => autowire(UserProfilePhotoUseCaseHandler::class),

    // Travel Use Cases
    CancelTravelUseCase::class => autowire(CancelTravelUseCaseHandler::class),
    CompleteTravelUseCase::class => autowire(CompleteTravelUseCaseHandler::class),
    RequestNewTravelUseCase::class => autowire(RequestNewTravelUseCaseHandler::class),
    StartAcceptedTravelUseCase::class => autowire(StartAcceptedTravelUseCaseHandler::class),
    RespondToTravelRequestUseCase::class => autowire(RespondToTravelRequestUseCaseHandler::class),
    GetTravelHistoryUseCase::class => autowire(GetTravelHistoryUseCaseHandler::class),
    RateTravelUseCase::class => autowire(RateTravelUseCaseHandler::class),
    GetTravelRatingsByTravelUseCase::class => autowire(GetTravelRatingsByTravelUseCaseHandler::class),

    // Emergency Use Cases
    EmergencyNumberGetUseCase::class => autowire(EmergencyNumberGetUseCaseHandler::class),
    EmergencyNumberSaveUseCase::class => autowire(EmergencyNumberSaveUseCaseHandler::class),

    // Incident Use Cases
    RegisterIncidentUseCase::class => autowire(RegisterIncidentUseCaseHandler::class),
];