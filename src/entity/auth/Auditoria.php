<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_auditoria")]
class Auditoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "audi_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "audi_tabla_afectada", type: "text")]
    private string $tablaAfectada;

    #[ORM\Column(name: "audi_operacion", type: "text")]
    private string $operacion;

    #[ORM\Column(name: "audi_usuario_sistema", type: "string", length: 100)]
    private string $usuarioSistema;

    #[ORM\Column(name: "audi_fecha", type: "datetime")]
    private \DateTime $fecha;

    #[ORM\Column(name: "audi_dato_anterior", type: "json", nullable: true)]
    private ?array $datoAnterior = null;

    #[ORM\Column(name: "audi_dato_nuevo", type: "json", nullable: true)]
    private ?array $datoNuevo = null;

    // Getters y setters
}