<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_contacto_usuario')]
class UserContactEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private UserEntity $user;
    #[ORM\ManyToOne(targetEntity: ContactTypeEntity::class)]
    #[ORM\JoinColumn(name: 'cont_tipo_id', referencedColumnName: 'tipo_id', nullable: false)]
    private ContactTypeEntity $type;
    #[ORM\Column(name: 'cont_valor', type: 'string', length: 100, unique: true, nullable: false)]
    private string $value;
    #[ORM\Column(name: 'cont_confirmado', type: 'boolean', nullable: false)]
    private bool $confirmed;
    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}