<?php

namespace itaxcix\Infrastructure\Database\Repository\rating;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\rating\RatingModel;
use itaxcix\Core\Interfaces\rating\RatingRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\rating\RatingEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineRatingRepository implements RatingRepositoryInterface
{
    private UserRepositoryInterface $userRepository;
    private TravelRepositoryInterface $travelRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TravelRepositoryInterface $travelRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->travelRepository = $travelRepository;
        $this->entityManager = $entityManager;
    }

    public function toDomain(RatingEntity $entity): RatingModel
    {
        return new RatingModel(
            id: $entity->getId(),
            rater: $this->userRepository->toDomain($entity->getRater()),
            rated: $this->userRepository->toDomain($entity->getRated()),
            travel: $this->travelRepository->toDomain($entity->getTravel()),
            score: $entity->getScore(),
            comment: $entity->getComment()
        );
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveRating(RatingModel $ratingModel): RatingModel
    {
        if ($ratingModel->getId()) {
            $entity = $this->entityManager->find(RatingEntity::class, $ratingModel->getId());
        } else {
            $entity = new RatingEntity();
        }

        $entity->setRater(
            $this->entityManager->getReference(
                UserEntity::class,
                $ratingModel->getRater()->getId()
            )
        );

        $entity->setRated(
            $this->entityManager->getReference(
                UserEntity::class,
                $ratingModel->getRated()->getId()
            )
        );

        $entity->setTravel(
            $this->entityManager->getReference(
                TravelEntity::class,
                $ratingModel->getTravel()->getId()
            )
        );

        $entity->setScore($ratingModel->getScore());
        $entity->setComment($ratingModel->getComment());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findRatingByTravelIdAndRaterId(int $travelId, int $raterId): ?RatingModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RatingEntity::class, 'r')
            ->where('r.travel = :travelId')
            ->andWhere('r.rater = :raterId')
            ->setParameter('travelId', $travelId)
            ->setParameter('raterId', $raterId)
            ->getQuery();

        $result = $query->getOneOrNullResult();

        return $result ? $this->toDomain($result) : null;
    }

    public function findRatingsByTravelId(int $travelId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RatingEntity::class, 'r')
            ->where('r.travel = :travelId')
            ->setParameter('travelId', $travelId)
            ->getQuery();

        $results = $query->getResult();

        return array_map([$this, 'toDomain'], $results);
    }

    public function findRatingsReceivedByUserId(int $userId, int $offset, int $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RatingEntity::class, 'r')
            ->where('r.rated = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $results = $query->getResult();

        return array_map([$this, 'toDomain'], $results);
    }

    public function countRatingsReceivedByUserId(int $userId): int
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(RatingEntity::class, 'r')
            ->where('r.rated = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function getAverageRatingForUser(int $userId): float
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('AVG(r.score) as avgRating')
            ->from(RatingEntity::class, 'r')
            ->where('r.rated = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        $result = $query->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function findReport(\itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('r, rater, raterPerson, rated, ratedPerson, t')
            ->from(RatingEntity::class, 'r')
            ->leftJoin('r.rater', 'rater')
            ->leftJoin('rater.person', 'raterPerson')
            ->leftJoin('r.rated', 'rated')
            ->leftJoin('rated.person', 'ratedPerson')
            ->leftJoin('r.travel', 't');

        if ($dto->raterId) {
            $qb->andWhere('rater.id = :raterId')
                ->setParameter('raterId', $dto->raterId);
        }
        if ($dto->ratedId) {
            $qb->andWhere('rated.id = :ratedId')
                ->setParameter('ratedId', $dto->ratedId);
        }
        if ($dto->travelId) {
            $qb->andWhere('t.id = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->minScore !== null) {
            $qb->andWhere('r.score >= :minScore')
                ->setParameter('minScore', $dto->minScore);
        }
        if ($dto->maxScore !== null) {
            $qb->andWhere('r.score <= :maxScore')
                ->setParameter('maxScore', $dto->maxScore);
        }
        if ($dto->comment) {
            $qb->andWhere('r.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }
        $allowedSort = [
            'id' => 'r.id',
            'raterId' => 'rater.id',
            'ratedId' => 'rated.id',
            'travelId' => 't.id',
            'score' => 'r.score'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'r.id';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof RatingEntity) {
                $rater = $entity->getRater();
                $raterPerson = $rater?->getPerson();
                $rated = $entity->getRated();
                $ratedPerson = $rated?->getPerson();
                $result[] = [
                    'id' => $entity->getId(),
                    'raterId' => $rater?->getId(),
                    'raterName' => $raterPerson ? trim(($raterPerson->getName() ?? '') . ' ' . ($raterPerson->getLastName() ?? '')) : null,
                    'ratedId' => $rated?->getId(),
                    'ratedName' => $ratedPerson ? trim(($ratedPerson->getName() ?? '') . ' ' . ($ratedPerson->getLastName() ?? '')) : null,
                    'travelId' => $entity->getTravel()?->getId(),
                    'score' => $entity->getScore(),
                    'comment' => $entity->getComment()
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(RatingEntity::class, 'r')
            ->leftJoin('r.rater', 'rater')
            ->leftJoin('r.rated', 'rated')
            ->leftJoin('r.travel', 't');
        if ($dto->raterId) {
            $qb->andWhere('rater.id = :raterId')
                ->setParameter('raterId', $dto->raterId);
        }
        if ($dto->ratedId) {
            $qb->andWhere('rated.id = :ratedId')
                ->setParameter('ratedId', $dto->ratedId);
        }
        if ($dto->travelId) {
            $qb->andWhere('t.id = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->minScore !== null) {
            $qb->andWhere('r.score >= :minScore')
                ->setParameter('minScore', $dto->minScore);
        }
        if ($dto->maxScore !== null) {
            $qb->andWhere('r.score <= :maxScore')
                ->setParameter('maxScore', $dto->maxScore);
        }
        if ($dto->comment) {
            $qb->andWhere('r.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

