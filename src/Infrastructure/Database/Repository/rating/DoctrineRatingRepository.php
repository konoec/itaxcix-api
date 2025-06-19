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
}