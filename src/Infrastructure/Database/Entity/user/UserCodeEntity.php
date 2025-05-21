<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_codigo_usuario')]
class UserCodeEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codi_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'codi_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;
    #[ORM\ManyToOne(targetEntity: UserCodeTypeEntity::class)]
    #[ORM\JoinColumn(name: 'codi_tipo_id', referencedColumnName: 'tipo_id')]
    private ?UserCodeTypeEntity $type = null;
    #[ORM\ManyToOne(targetEntity: UserContactEntity::class)]
    #[ORM\JoinColumn(name: 'codi_contacto_id', referencedColumnName: 'cont_id')]
    private ?UserContactEntity $contact = null;
    #[ORM\Column(name: 'codi_codigo', type: 'string', length: 8)]
    private string $code;
    #[ORM\Column(name: 'codi_fecha_expiracion', type: 'datetime')]
    private DateTime $expirationDate;
    #[ORM\Column(name: 'codi_fecha_uso', type: 'datetime', nullable: true)]
    private ?DateTime $useDate = null;
    #[ORM\Column(name: 'codi_usado', type: 'boolean', options: ['default' => false])]
    private bool $used = false;
}