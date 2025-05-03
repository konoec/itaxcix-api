<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_modalidad_tuc')]
class ModalidadTuc {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'moda_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'moda_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'moda_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}