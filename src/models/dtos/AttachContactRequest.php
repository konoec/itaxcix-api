<?php

namespace itaxcix\models\dtos;

use InvalidArgumentException;
use itaxcix\validators\ContactTypeValidator;
use itaxcix\validators\ContactValidator;

class AttachContactRequest {
    public int $userId;
    public string $contact;
    public int $contactTypeId;
    public function __construct(array $data) {
        $this->userId = $data['userId'] ?? throw new InvalidArgumentException('userId es requerido');
        $this->contact = $data['contact'] ?? throw new InvalidArgumentException('contact es requerido');
        $this->contactTypeId = $data['contactTypeId'] ?? throw new InvalidArgumentException('contactTypeId es requerido');

        ContactTypeValidator::validate($this->contactTypeId);
        ContactValidator::validate($this->contact, $this->contactTypeId);
    }
}
