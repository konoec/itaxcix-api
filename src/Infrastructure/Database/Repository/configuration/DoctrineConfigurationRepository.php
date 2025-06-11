<?php

namespace itaxcix\Infrastructure\Database\Repository\configuration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\configuration\ConfigurationModel;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\configuration\ConfigurationEntity;

class DoctrineConfigurationRepository implements ConfigurationRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ConfigurationEntity $entity): ConfigurationModel
    {
        return new ConfigurationModel(
            id: $entity->getId(),
            key: $entity->getKey(),
            value: $entity->getValue(),
            active: $entity->isActive()
        );
    }

    public function findConfigurationByKey(string $key): ?ConfigurationModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ConfigurationEntity::class, 'c')
            ->where('c.key = :key')
            ->andWhere('c.active = true')
            ->setParameter('key', $key)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveConfiguration(ConfigurationModel $configurationModel): ConfigurationModel
    {
        if ($configurationModel->getId()) {
            $entity = $this->entityManager->find(ConfigurationEntity::class, $configurationModel->getId());
        } else {
            $entity = new ConfigurationEntity();
        }

        $entity->setKey($configurationModel->getKey());
        $entity->setValue($configurationModel->getValue());
        $entity->setActive($configurationModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}

