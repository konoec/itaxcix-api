<?php

namespace itaxcix\Core\Handler\User;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\User\UserProfilePhotoUploadUseCase;
use itaxcix\Shared\DTO\useCases\User\UserProfilePhotoUploadRequestDTO;

class UserProfilePhotoUploadUseCaseHandler implements UserProfilePhotoUploadUseCase
{
    private PersonRepositoryInterface $personRepository;
    private UserRepositoryInterface $userRepository;
    private string $uploadDirectory;

    public function __construct(
        PersonRepositoryInterface $personRepository,
        UserRepositoryInterface $userRepository,
        string $uploadDirectory = 'uploads/profile_photos'
    ) {
        $this->personRepository = $personRepository;
        $this->userRepository = $userRepository;
        $this->uploadDirectory = $uploadDirectory;
    }

    public function execute(UserProfilePhotoUploadRequestDTO $request): ?array
    {
        // Verificar que el usuario existe
        $user = $this->userRepository->findUserById($request->userId);

        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        // Asegurar que el directorio existe
        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }

        // Extraer la parte de datos del Base64
        $base64Image = $request->imageBase64;

        // Si la cadena base64 incluye información de tipo MIME, extraer solo los datos
        if (preg_match('/^data:image\/[a-zA-Z]+;base64,/', $base64Image)) {
            $base64Image = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64Image);
        }

        // Decodificar la imagen base64
        $decodedImage = base64_decode($base64Image);

        if ($decodedImage === false) {
            throw new InvalidArgumentException('Error al decodificar la imagen base64');
        }

        // Guardar la imagen en el sistema de archivos
        $fileName = $user->getPerson()->getId() . '.jpg';
        $filePath = $this->uploadDirectory . '/' . $fileName;

        if (file_put_contents($filePath, $decodedImage) === false) {
            throw new InvalidArgumentException('Error al guardar la imagen en el servidor');
        }

        $person = $user->getPerson();
        $person->setImage($filePath);

        $this->personRepository->savePerson($person);

        return [
            'message' => 'Foto de perfil actualizada correctamente'
        ];
    }
}