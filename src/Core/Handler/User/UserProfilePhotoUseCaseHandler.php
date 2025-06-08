<?php

namespace itaxcix\Core\Handler\User;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\User\UserProfilePhotoUseCase;
use itaxcix\Shared\DTO\useCases\User\UserProfilePhotoResponseDTO;

class UserProfilePhotoUseCaseHandler implements UserProfilePhotoUseCase
{
    private UserRepositoryInterface $userRepository;
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function execute(int $userId): ?UserProfilePhotoResponseDTO
    {
        // Verificar que el usuario existe
        $user = $this->userRepository->findUserById($userId);

        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        if (!$user->getPerson()->getImage()) {
            throw new InvalidArgumentException('Usuario no tiene foto de perfil');
        }

        // Construir la ruta de la imagen
        $photoPath = $user->getPerson()->getImage();

        // Verificar si existe la imagen
        if (!file_exists($photoPath)) {
            throw new InvalidArgumentException('Foto de perfil no encontrada');
        }

        // Obtener el tipo MIME
        $mimeType = mime_content_type($photoPath);

        // Leer la imagen y convertir a base64
        $imageData = file_get_contents($photoPath);
        $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

        return new UserProfilePhotoResponseDTO($userId, $base64Image);
    }
}