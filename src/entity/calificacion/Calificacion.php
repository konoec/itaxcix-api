<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_calificacion")]
class Calificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "cali_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cali_puntaje", type: "integer")]
    private int $puntaje;

    #[ORM\Column(name: "cali_comentario", type: "string", length: 255, nullable: true)]
    private ?string $comentario = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "cali_calificador_id", referencedColumnName: "usua_id")]
    private Usuario $calificador;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "cali_calificado_id", referencedColumnName: "usua_id")]
    private Usuario $calificado;

    #[ORM\ManyToOne(targetEntity: Viaje::class)]
    #[ORM\JoinColumn(name: "cali_viaje_id", referencedColumnName: "viaj_id")]
    private Viaje $viaje;

    // Getters y setters
}