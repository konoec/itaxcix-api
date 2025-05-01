<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_persona")]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "pers_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "pers_nombre", type: "string", length: 100, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: "pers_apellido", type: "string", length: 100, nullable: true)]
    private ?string $apellido = null;

    #[ORM\Column(name: "pers_documento", type: "string", length: 20, nullable: true)]
    private ?string $documento = null;

    #[ORM\Column(name: "pers_telefono", type: "string", length: 15, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(name: "pers_correo", type: "string", length: 100, nullable: true)]
    private ?string $correo = null;

    #[ORM\Column(name: "pers_imagen", type: "string", length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(name: "pers_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: \TipoDocumento::class)]
    #[ORM\JoinColumn(name: "pers_tipo_documento_id", referencedColumnName: "tipo_id", nullable: true)]
    private ?\TipoDocumento $tipoDocumento = null;

    #[ORM\OneToOne(mappedBy: "persona", targetEntity: Usuario::class)]
    private ?Usuario $usuario = null;

    // Getters y setters
}