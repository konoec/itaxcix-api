<?php

namespace itaxcix\model\entities\tuc;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_ruta_servicio')]
class RutaServicio {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ruta_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TramiteTuc::class)]
    #[ORM\JoinColumn(name: 'ruta_tram_id', referencedColumnName: 'tram_id')]
    private ?TramiteTuc $tramite = null;

    #[ORM\Column(name: 'ruta_tipo_servicio', type: 'string', length: 50, nullable: true)]
    private ?string $tipoServicio = null;

    #[ORM\Column(name: 'ruta_texto', type: 'text', nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(name: 'ruta_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}