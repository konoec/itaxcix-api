<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_empresa")]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "empr_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "empr_ruc", type: "string", length: 11, nullable: true)]
    private ?string $ruc = null;

    #[ORM\Column(name: "empr_nombre", type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(name: "empr_propietario", type: "string", length: 100, nullable: true)]
    private ?string $propietario = null;

    #[ORM\Column(name: "empr_gob_local", type: "string", length: 100, nullable: true)]
    private ?string $gobLocal = null;

    #[ORM\Column(name: "empr_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}