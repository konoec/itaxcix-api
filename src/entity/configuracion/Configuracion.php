<?php

namespace itaxcix\entity\configuracion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_configuracion')]
class Configuracion {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'conf_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'conf_clave', type: 'string', length: 50)]
    private string $clave;

    #[ORM\Column(name: 'conf_valor', type: 'string', length: 255)]
    private string $valor;

    #[ORM\Column(name: 'conf_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}