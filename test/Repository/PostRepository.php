<?php

namespace Kocal\Expressive\Database\Doctrine\Test\Repository;

use Kocal\Expressive\Database\Doctrine\DoctrineRepository;
use Kocal\Expressive\Database\Doctrine\Test\Entity\Post;

/**
 * Class PostRepository
 * @package Kocal\Expressive\Database\Doctrine\Test
 */
class PostRepository extends DoctrineRepository
{
    /**
     * @return Post[]
     */
    public function getTwoLastPosts()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->setMaxResults(2)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}