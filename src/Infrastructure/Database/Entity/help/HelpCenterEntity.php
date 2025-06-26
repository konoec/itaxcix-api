<?php

namespace itaxcix\Infrastructure\Database\Entity\help;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_centro_ayuda')]
class HelpCenterEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ayuda_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'ayuda_titulo', type: 'string', length: 100)]
    private string $title;

    #[ORM\Column(name: 'ayuda_subtitulo', type: 'string', length: 150)]
    private string $subtitle;

    #[ORM\Column(name: 'ayuda_respuesta', type: 'text')]
    private string $answer;

    #[ORM\Column(name: 'ayuda_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
