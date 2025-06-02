<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DepartmentEntity;
use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;

class DoctrineProvinceRepository implements ProvinceRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(EntityManagerInterface $entityManager, DepartmentRepositoryInterface $departmentRepository){
        $this->entityManager = $entityManager;
        $this->departmentRepository = $departmentRepository;
    }

    public function toDomain(ProvinceEntity $entity): ProvinceModel
    {
        return new ProvinceModel(
            id: $entity->getId(),
            name: $entity->getName(),
            department: $this->departmentRepository->toDomain($entity->getDepartment()),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findProvinceByName(string $name): ?ProvinceModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProvinceEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveProvince(ProvinceModel $provinceModel): ProvinceModel
    {
        if ($provinceModel->getId()) {
            $entity = $this->entityManager->find(ProvinceEntity::class, $provinceModel->getId());
        } else {
            $entity = new ProvinceEntity();
        }

        $entity->setName($provinceModel->getName());
        $entity->setDepartment(
            $this->entityManager->getReference(
                DepartmentEntity::class, $provinceModel->getDepartment()->getId()
            )
        );
        $entity->setUbigeo($provinceModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}