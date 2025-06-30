<?php

namespace itaxcix\Shared\DTO\useCases\RatingReport;

class RatingReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?int $raterId = null,
        public readonly ?int $ratedId = null,
        public readonly ?int $travelId = null,
        public readonly ?int $minScore = null,
        public readonly ?int $maxScore = null,
        public readonly ?string $comment = null,
        public readonly ?string $sortBy = 'id',
        public readonly ?string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            raterId: isset($data['raterId']) ? (int)$data['raterId'] : null,
            ratedId: isset($data['ratedId']) ? (int)$data['ratedId'] : null,
            travelId: isset($data['travelId']) ? (int)$data['travelId'] : null,
            minScore: isset($data['minScore']) ? (int)$data['minScore'] : null,
            maxScore: isset($data['maxScore']) ? (int)$data['maxScore'] : null,
            comment: $data['comment'] ?? null,
            sortBy: $data['sortBy'] ?? 'id',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }
}

