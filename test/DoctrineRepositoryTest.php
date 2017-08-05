<?php

namespace Kocal\Expressive\Database\Doctrine\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Kocal\Expressive\Database\Doctrine\DoctrineRepository;
use Kocal\Expressive\Database\Doctrine\EntityManagerFactory;
use Kocal\Expressive\Database\Doctrine\Test\Entity\Post;
use Kocal\Expressive\Database\Doctrine\Test\Repository\PostRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class DoctrineRepositoryTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function setUp()
    {
        $this->createEntityManager();
        $this->createSchema();
        $this->initializePosts();
    }

    public function tearDown()
    {
        $this->dropSchema();
    }

    public function testEntityManager()
    {
        $this->assertTrue($this->entityManager->isOpen());
    }

    /**
     * @see DoctrineRepository::all()
     */
    public function testAll()
    {
        /** @var Post[] $posts */
        $posts = $this->postRepository->all();

        $this->assertCount(3, $posts);
        $this->assertEquals("First post", $posts[0]->getTitle());
        $this->assertEquals("Second post", $posts[1]->getTitle());
        $this->assertEquals("Third post", $posts[2]->getTitle());
    }

    /**
     * @see DoctrineRepository::first()
     */
    public function testFirst()
    {
        /** @var Post $post */
        $post = $this->postRepository->first();

        $this->assertEquals("First post", $post->getTitle());
    }

    /**
     * @see DoctrineRepository::last()
     */
    public function testLast()
    {
        /** @var Post $post */
        $post = $this->postRepository->last();

        $this->assertEquals("Third post", $post->getTitle());
    }

    /**
     * @see DoctrineRepository::findByField()
     */
    public function testFindByField()
    {
        /** @var Post $post */
        $post = $this->postRepository->findByField('title', 'Second post');

        $this->assertEquals("Second post", $post->getTitle());
    }

    /**
     * @see DoctrineRepository::findWhere()
     */
    public function testFindWhere()
    {
        /** @var Post[] $posts */
        $posts = $this->postRepository->findWhere([
            'id' => 3
        ]);

        $this->assertCount(1, $posts);
        $this->assertEquals("Third post", $posts[0]->getTitle());
    }

    /**
     * @see DoctrineRepository::findWhereIn()
     */
    public function testFindWhereIn()
    {
        /** @var Post[] $posts */
        $posts = $this->postRepository->findWhereIn('id', [1, 3]);

        $this->assertCount(2, $posts);
        $this->assertEquals("First post", $posts[0]->getTitle());
        $this->assertEquals("Third post", $posts[1]->getTitle());
    }

    /**
     * @see DoctrineRepository::findWhereNotIn()
     */
    public function testFindWhereNotIn()
    {
        /** @var Post[] $posts */
        $posts = $this->postRepository->findWhereNotIn('id', [1, 3]);

        $this->assertCount(1, $posts);
        $this->assertEquals("Second post", $posts[0]->getTitle());
    }

    /**
     * @see DoctrineRepository::save()
     */
    public function testSaveByCreatingANewEntity()
    {
        $post = new Post("Fourth post", "My fourth post");
        $this->assertNull($post->getId());
        $this->postRepository->save($post);

        $this->assertEquals(4, $post->getId());
        $this->assertCount(4, $this->postRepository->all());
    }

    /**
     * @see DoctrineRepository::save()
     */
    public function testSaveByUpdatingAnExistingEntity()
    {
        /** @var Post $post */
        $post = $this->postRepository->first();
        $post->setTitle("Updated first post");
        $this->postRepository->save($post);

        /** @var Post $post */
        $post = $this->postRepository->first();
        $this->assertEquals("Updated first post", $post->getTitle());
    }

    /**
     * @see DoctrineRepository::delete()
     */
    public function testDeleteByUsingEntity()
    {
        /** @var Post $post */
        $post = $this->postRepository->first();
        $this->postRepository->delete($post);

        $this->assertCount(2, $this->postRepository->all());
    }

    public function testDeleteByUsingEntityId()
    {
        $this->postRepository->delete(1);

        $this->assertCount(2, $this->postRepository->all());
    }

    /**
     * Test custom method from PostRepository.
     * @see PostRepository::getTwoLastPosts()
     */
    public function testGetTwoLastPosts()
    {

        $posts = $this->postRepository->getTwoLastPosts();

        $this->assertCount(2, $posts);
        $this->assertEquals("Third post", $posts[0]->getTitle());
        $this->assertEquals("Second post", $posts[1]->getTitle());
    }

    private function createEntityManager()
    {
        $entityManagerFactory = new EntityManagerFactory();
        $this->entityManager = $entityManagerFactory($this->getContainer());
    }

    private function getContainer()
    {
        $mock = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('get')
            ->willReturn([
                'debug' => true,
                'doctrine' => [
                    'driver' => 'pdo_sqlite',
                    'path' => ':memory:',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                ],
                'entities_path' => [
                    __DIR__ . '/Entity'
                ],
            ]);

        return $mock;
    }

    private function createSchema()
    {
        $tool = new SchemaTool($this->entityManager);
        $tool->createSchema([
            $this->entityManager->getClassMetadata(Post::class)
        ]);
    }

    private function dropSchema()
    {
        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema([
            $this->entityManager->getClassMetadata(Post::class)
        ]);
    }

    private function initializePosts()
    {
        $this->postRepository = $this->entityManager->getRepository(Post::class);

        $this->postRepository->save(new Post("First post", "My first post"));
        $this->postRepository->save(new Post("Second post", "My second post"));
        $this->postRepository->save(new Post("Third post", "My third post"));
    }
}