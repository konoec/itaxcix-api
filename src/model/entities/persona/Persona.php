<?php

namespace itaxcix\model\entities\persona;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_persona')]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'pers_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'pers_nombre', type: 'string', length: 100, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'pers_apellido', type: 'string', length: 100, nullable: true)]
    private ?string $apellido = null;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    #[ORM\JoinColumn(name: 'pers_tipo_documento_id', referencedColumnName: 'tipo_id')]
    private ?TipoDocumento $tipoDocumento = null;

    #[ORM\Column(name: 'pers_documento', type: 'string', length: 20, nullable: true)]
    private ?string $documento = null;

    #[ORM\Column(name: 'pers_imagen', type: 'string', length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(name: 'pers_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}