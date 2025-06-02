<?php

namespace itaxcix\Core\UseCases;

use DateTime;
use Exception;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Shared\DTO\useCases\BiometricValidationRequestDTO;
use RuntimeException;
use svay\FaceDetector;

class BiometricValidationUseCaseHandler implements BiometricValidationUseCase
{
    private PersonRepositoryInterface $personRepository;
    public function __construct(PersonRepositoryInterface $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    public function execute(BiometricValidationRequestDTO $dto): ?array
    {
        $person = $this->personRepository->findPersonById($dto->personId);

        if ($person === null) {
            throw new InvalidArgumentException('La persona no existe o no está activa.');
        }

        $validationDate = $person->getValidationDate();
        if ($validationDate !== null) {
            $now = new DateTime();
            $validationDatePlus5 = (clone $validationDate)->modify('+5 minutes');
            if ($validationDatePlus5 > $now) {
                $remaining = $validationDatePlus5->getTimestamp() - $now->getTimestamp();
                $minutes = floor($remaining / 60);
                $seconds = $remaining % 60;
                throw new InvalidArgumentException(
                    'Ya se está haciendo la validación en otro dispositivo. Espere ' .
                    sprintf('%02d:%02d', $minutes, $seconds) . ' minutos.'
                );
            }
        }

        // Aquí debería verificar el biométrico, si es una imagen facial de una persona.
        // Por ahora: Validar que la imagen tenga un rostro humano
        if (!$this->detectarRostroDesdeBase64($dto->imageBase64)) {
            throw new InvalidArgumentException('No se detectó un rostro válido en la imagen.');
        }

        $person->setValidationDate(new DateTime());

        $this->personRepository->savePerson($person);

        return [
            'personId' => $person->getId(),
            'validationDate' => $person->getValidationDate()->format('Y-m-d H:i:s')
        ];
    }

    private function detectarRostroDesdeBase64(string $base64): bool
    {
        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);

        $imagenBinaria = base64_decode($base64);

        $rutaTemporal = sys_get_temp_dir() . '/imagen_' . uniqid() . '.jpg';
        file_put_contents($rutaTemporal, $imagenBinaria);

        $detector = new FaceDetector();

        try {
            // Silencia temporalmente los warnings (no recomendado para producción)
            set_error_handler(function () {}, E_WARNING | E_DEPRECATED);
            $detector->faceDetect($rutaTemporal);
            restore_error_handler();
        } catch (Exception $e) {
            restore_error_handler(); // Asegúrate de restaurarlo en el catch también
            throw new RuntimeException('Error al detectar el rostro: ' . $e->getMessage(), 0, $e);
        }


        unlink($rutaTemporal);

        return $detector->getFace() !== null;
    }
}