<?php

namespace itaxcix\Infrastructure\Database\Entity\configuration;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_configuracion')]
class ConfigurationEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'conf_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'conf_clave', type: 'string', length: 50)]
    private string $key;
    #[ORM\Column(name: 'conf_valor', type: 'string', length: 255)]
    private string $value;
    #[ORM\Column(name: 'conf_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}