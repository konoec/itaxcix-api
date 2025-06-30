<?php

namespace itaxcix\Infrastructure\Database\Entity\audit;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_auditoria')]
class AuditEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'audi_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'audi_tabla_afectada', type: 'text')]
    private string $affectedTable;
    #[ORM\Column(name: 'audi_operacion', type: 'text')]
    private string $operation;
    #[ORM\Column(name: 'audi_usuario_sistema', type: 'string', length: 100)]
    private string $systemUser;
    #[ORM\Column(name: 'audi_fecha', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $date;
    #[ORM\Column(name: 'audi_dato_anterior', type: 'json', nullable: true)]
    private ?array $previousData = null;
    #[ORM\Column(name: 'audi_dato_nuevo', type: 'json', nullable: true)]
    private ?array $newData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getAffectedTable(): string
    {
        return $this->affectedTable;
    }

    public function setAffectedTable(string $affectedTable): void
    {
        $this->affectedTable = $affectedTable;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    public function getSystemUser(): string
    {
        return $this->systemUser;
    }

    public function setSystemUser(string $systemUser): void
    {
        $this->systemUser = $systemUser;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getPreviousData(): ?array
    {
        return $this->previousData;
    }

    public function setPreviousData(?array $previousData): void
    {
        $this->previousData = $previousData;
    }

    public function getNewData(): ?array
    {
        return $this->newData;
    }

    public function setNewData(?array $newData): void
    {
        $this->newData = $newData;
    }


}