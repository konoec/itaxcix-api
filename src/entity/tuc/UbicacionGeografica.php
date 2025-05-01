<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_ubicacion_geografica")]
class UbicacionGeografica
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ubic_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "ubic_ubigeo", type: "string", length: 6, nullable: true)]
    private ?string $ubigeo = null;

    #[ORM\Column(name: "ubic_departamento", type: "string", length: 50, nullable: true)]
    private ?string $departamento = null;

    #[ORM\Column(name: "ubic_provincia", type: "string", length: 50, nullable: true)]
    private ?string $provincia = null;

    #[ORM\Column(name: "ubic_distrito", type: "string", length: 50, nullable: true)]
    private ?string $distrito = null;

    // Getters y setters
}