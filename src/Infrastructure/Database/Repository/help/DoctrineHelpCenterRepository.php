<?php

namespace itaxcix\Infrastructure\Database\Repository\help;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\help\HelpCenterModel;
use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\help\HelpCenterEntity;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

class DoctrineHelpCenterRepository implements HelpCenterRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(HelpCenterEntity $entity): HelpCenterModel
    {
        return new HelpCenterModel(
            id: $entity->getId(),
            title: $entity->getTitle(),
            subtitle: $entity->getSubtitle(),
            answer: $entity->getAnswer(),
            active: $entity->isActive()
        );
    }

    public function findAllActiveHelpItems(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('h')
            ->from(HelpCenterEntity::class, 'h')
            ->where('h.active = true')
            ->orderBy('h.title', 'ASC')
            ->addOrderBy('h.subtitle', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map(fn($entity) => $this->toDomain($entity), $entities);
    }

    public function findAllHelpItems(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('h')
            ->from(HelpCenterEntity::class, 'h')
            ->orderBy('h.title', 'ASC')
            ->addOrderBy('h.subtitle', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map(fn($entity) => $this->toDomain($entity), $entities);
    }

    public function findHelpItemById(int $id): ?HelpCenterModel
    {
        $entity = $this->entityManager->find(HelpCenterEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveHelpItem(HelpCenterModel $helpItem): HelpCenterModel
    {
        if ($helpItem->getId()) {
            $entity = $this->entityManager->find(HelpCenterEntity::class, $helpItem->getId());
        } else {
            $entity = new HelpCenterEntity();
        }

        $entity->setTitle($helpItem->getTitle());
        $entity->setSubtitle($helpItem->getSubtitle());
        $entity->setAnswer($helpItem->getAnswer());
        $entity->setActive($helpItem->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function deleteHelpItem(int $id): bool
    {
        $entity = $this->entityManager->find(HelpCenterEntity::class, $id);
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function findAllHelpItemsPaginated(int $page, int $perPage): PaginationResponseDTO
    {
        $offset = ($page - 1) * $perPage;

        // Query para obtener el total de elementos
        $totalQuery = $this->entityManager->createQueryBuilder()
            ->select('COUNT(h.id)')
            ->from(HelpCenterEntity::class, 'h')
            ->getQuery();

        $total = (int) $totalQuery->getSingleScalarResult();

        // Query para obtener los elementos paginados
        $query = $this->entityManager->createQueryBuilder()
            ->select('h')
            ->from(HelpCenterEntity::class, 'h')
            ->orderBy('h.title', 'ASC')
            ->addOrderBy('h.subtitle', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery();

        $entities = $query->getResult();
        $items = array_map(fn($entity) => $this->toDomain($entity), $entities);

        $lastPage = (int) ceil($total / $perPage);

        $meta = new PaginationMetaDTO(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }
}
