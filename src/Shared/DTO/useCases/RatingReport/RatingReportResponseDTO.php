<?php

namespace itaxcix\Shared\DTO\useCases\RatingReport;

class RatingReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $raterId,
        public readonly ?string $raterName,
        public readonly ?int $ratedId,
        public readonly ?string $ratedName,
        public readonly ?int $travelId,
        public readonly int $score,
        public readonly ?string $comment
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'raterId' => $this->raterId,
            'raterName' => $this->raterName,
            'ratedId' => $this->ratedId,
            'ratedName' => $this->ratedName,
            'travelId' => $this->travelId,
            'score' => $this->score,
            'comment' => $this->comment
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            raterId: $data['raterId'] ?? null,
            raterName: $data['raterName'] ?? null,
            ratedId: $data['ratedId'] ?? null,
            ratedName: $data['ratedName'] ?? null,
            travelId: $data['travelId'] ?? null,
            score: $data['score'],
            comment: $data['comment'] ?? null
        );
    }
}

