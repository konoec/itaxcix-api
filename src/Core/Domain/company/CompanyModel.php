<?php

namespace itaxcix\Core\Domain\company;

class CompanyModel {
    private int $id;
    private ?string $ruc = null;
    private string $name;
    private bool $active = true;
}