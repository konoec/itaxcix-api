<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_codigo_usuario')]
class CodigoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codi_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'codi_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: TipoCodigoUsuario::class)]
    #[ORM\JoinColumn(name: 'codi_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoCodigoUsuario $tipo = null;

    #[ORM\ManyToOne(targetEntity: ContactoUsuario::class)]
    #[ORM\JoinColumn(name: 'codi_contacto_id', referencedColumnName: 'cont_id')]
    private ?ContactoUsuario $contacto = null;

    #[ORM\Column(name: 'codi_codigo', type: 'string', length: 8)]
    private string $codigo;

    #[ORM\Column(name: 'codi_fecha_expiracion', type: 'datetime')]
    private \DateTime $fechaExpiracion;

    #[ORM\Column(name: 'codi_fecha_uso', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaUso = null;

    #[ORM\Column(name: 'codi_usado', type: 'boolean', options: ['default' => false])]
    private bool $usado = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function getTipo(): ?TipoCodigoUsuario
    {
        return $this->tipo;
    }

    public function setTipo(?TipoCodigoUsuario $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getContacto(): ?ContactoUsuario
    {
        return $this->contacto;
    }

    public function setContacto(?ContactoUsuario $contacto): void
    {
        $this->contacto = $contacto;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getFechaExpiracion(): \DateTime
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTime $fechaExpiracion): void
    {
        $this->fechaExpiracion = $fechaExpiracion;
    }

    public function getFechaUso(): ?\DateTime
    {
        return $this->fechaUso;
    }

    public function setFechaUso(?\DateTime $fechaUso): void
    {
        $this->fechaUso = $fechaUso;
    }

    public function isUsado(): bool
    {
        return $this->usado;
    }

    public function setUsado(bool $usado): void
    {
        $this->usado = $usado;
    }
}