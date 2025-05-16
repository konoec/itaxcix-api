<?php

namespace itaxcix\models\dtos;

use InvalidArgumentException;
use itaxcix\validators\ContactValidator;

class DetachContactRequest {
    public int $userId;
    public string $contact;
    public function __construct() {
        $this->userId = $data['userId'] ?? throw new InvalidArgumentException('userId es requerido');
        $this->contact = $data['contact'] ?? throw new InvalidArgumentException('contact es requerido');

        ContactValidator::validate($this->contact);
    }
}