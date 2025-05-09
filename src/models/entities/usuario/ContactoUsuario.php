<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\usuario\ContactoUsuarioRepository;

#[ORM\Entity(repositoryClass: ContactoUsuarioRepository::class)]
#[ORM\Table(name: 'tb_contacto_usuario')]
class ContactoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'contactos')]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: TipoContacto::class)]
    #[ORM\JoinColumn(name: 'cont_tipo_id', referencedColumnName: 'tipo_id', nullable: false)]
    private ?TipoContacto $tipo = null;

    #[ORM\Column(name: 'cont_valor', type: 'string', length: 100, unique: true, nullable: false)]
    private string $valor;

    #[ORM\Column(name: 'cont_confirmado', type: 'boolean', nullable: false)]
    private bool $confirmado;

    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getUsuario(): ?Usuario {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): void {
        $this->usuario = $usuario;
    }

    public function getTipo(): ?TipoContacto {
        return $this->tipo;
    }

    public function setTipo(?TipoContacto $tipo): void {
        $this->tipo = $tipo;
    }

    public function getValor(): string {
        return $this->valor;
    }

    public function setValor(string $valor): void {
        $this->valor = $valor;
    }

    public function isConfirmado(): bool {
        return $this->confirmado;
    }

    public function setConfirmado(bool $confirmado): void {
        $this->confirmado = $confirmado;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }
}