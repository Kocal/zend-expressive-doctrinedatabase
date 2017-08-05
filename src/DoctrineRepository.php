<?php

namespace Kocal\Expressive\Database\Doctrine;

use Doctrine\ORM\EntityRepository;
use Kocal\Expressive\Database\DatabaseRepositoryInterface;

class DoctrineRepository extends EntityRepository implements DatabaseRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        return $this->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * {@inheritdoc}
     */
    public function findByField($field, $value, $orderBy = null)
    {
        return $this->findOneBy([
            $field => $value
        ], $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findWhere(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds Entities where `$column` is in `$where` array.
     * @param string $column
     * @param array $where
     * @return mixed
     */
    public function findWhereIn($column, array $where)
    {
        $column = $this->getEntityManager()->getConnection()->quote($column);

        return $this
            ->createQueryBuilder('v')
            ->select('v')
            ->where("v.${column} IN (:values)")
            ->setParameter(':values', $where)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds Entities where `$column` is not in `$where` array.
     * @param string $column
     * @param array $where
     * @return mixed
     */
    public function findWhereNotIn($column, array $where)
    {
        $column = $this->getEntityManager()->getConnection()->quote($column);

        return $this
            ->createQueryBuilder('v')
            ->select('v')
            ->where("v.${column} NOT IN (:values)")
            ->setParameter(':values', $where)
            ->getQuery()
            ->getResult();
    }

    /**
     * Update an Entity by its ID or by itself.
     * @param object $entity
     */
    public function save($entity)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * Delete an Entity by its ID or by itself.
     * @param int|object $idOrEntity
     * @return mixed
     */
    public function delete($idOrEntity)
    {
        if (is_int($idOrEntity)) {
            $idOrEntity = $this->find($idOrEntity);
        }

        $entityManager = $this->getEntityManager();
        $entityManager->remove($idOrEntity);
        $entityManager->flush();
    }
}