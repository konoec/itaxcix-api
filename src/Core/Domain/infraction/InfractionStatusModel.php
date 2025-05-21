<?php

namespace itaxcix\Core\Domain\infraction;

class InfractionStatusModel {
    private int $id;
    private string $name;
    private bool $active = true;
}