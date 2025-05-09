<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\entities\persona\Persona;
use itaxcix\repositories\usuario\UsuarioRepository;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: 'tb_usuario')]
class Usuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'usua_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'usua_alias', type: 'string', length: 50, unique: true, nullable: false)]
    private string $alias;

    #[ORM\Column(name: 'usua_clave', type: 'string', length: 255, nullable: false)]
    private string $clave;

    #[ORM\OneToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'usua_persona_id', referencedColumnName: 'pers_id', nullable: false)]
    private ?Persona $persona = null;

    #[ORM\ManyToOne(targetEntity: EstadoUsuario::class)]
    #[ORM\JoinColumn(name: 'usua_estado_id', referencedColumnName: 'esta_id', nullable: false)]
    private ?EstadoUsuario $estado = null;

    #[ORM\OneToMany(
        targetEntity: ContactoUsuario::class,
        mappedBy: 'usuario',
        orphanRemoval: true
    )]
    private Collection $contactos;

    public function __construct() {
        $this->contactos = new ArrayCollection();
    }

    public function getContactos(): Collection {
        return $this->contactos;
    }

    public function addContacto(ContactoUsuario $contacto): void {
        if (!$this->contactos->contains($contacto)) {
            $this->contactos[] = $contacto;
            $contacto->setUsuario($this);
        }
    }

    public function removeContacto(ContactoUsuario $contacto): void {
        if ($this->contactos->removeElement($contacto)) {
            // set the owning side to null (unless already changed)
            if ($contacto->getUsuario() === $this) {
                $contacto->setUsuario(null);
            }
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getAlias(): string {
        return $this->alias;
    }

    public function setAlias(string $alias): void {
        $this->alias = $alias;
    }

    public function getClave(): string {
        return $this->clave;
    }

    public function setClave(string $clave): void {
        $this->clave = $clave;
    }

    public function getPersona(): ?Persona {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): void {
        $this->persona = $persona;
    }

    public function getEstado(): ?EstadoUsuario {
        return $this->estado;
    }

    public function setEstado(?EstadoUsuario $estado): void {
        $this->estado = $estado;
    }
}