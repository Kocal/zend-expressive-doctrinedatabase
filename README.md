Zend-Expressive Doctrine Database
=================================

# Installation

## Composer
Run `composer require kocal/zend-expressive-doctrinedatabase`

## Zend-Expressive configuration

In a Zend-Expressive configuration file (e.g.: `config/autoload/database.global.php` if you used Zend-Expressive app generator):

```php
<?php

use Doctrine\ORM\EntityManager;
use Kocal\Expressive\Database\Doctrine\EntityManagerFactory;

return [
    'dependencies' => [
        'factories' => [
            // Use EntityManagerFactory for using Doctrine EntityManager:
            EntityManager::class => EntityManagerFactory::class
        ]
    ],

    'doctrine' => [
        // DBAL configuration. More at http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../../database/database.sqlite',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],

    'entities_path' => [
        // Path to Entity, e.g.: `/path/to/project/src/App/Entity`
    ],
];
```

Now let's create an Entity and its Repository.

## Usage

### Creating an Entity

Let's say our Entities are located in `src/App/Entity` folder.

Example of a `Post` Entity:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table(name="posts") 
 */
class Post {
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * Post constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}

```

### Creating a Repository

A Repository should extends from [`DoctrineRepository`](./src/DoctrineRepository.php) class, which implements [`DatabaseRepositoryInterface`](https://github.com/Kocal/zend-expressive-database/blob/master/src/DatabaseRepositoryInterface.php)

```php
<?php

namespace App\Repository;

use App\Entity\Post;
use Kocal\Expressive\Database\Doctrine\DoctrineRepository;

/**
 * Class PostRepository
 */
class PostRepository extends DoctrineRepository
{
    // Some methods are already implemented.
    // Do not hesitate to read `DoctrineRepository.php`!
    
    // You can add custom methods
    
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

```

### Using a Repository:

```php
<?php

use App\Entity\Post;
use Doctrine\ORM\EntityManager;

// Retrieve EntityManager in the Container 
$em = $container->get(EntityManager::class);

// Retrieve PostRepository
$postRepository = $em->getRepository(Post::class);

// Use it!
$allPosts = $postRepository->all();
$firstPost = $postRepository->first();
$lastPost = $postRepository->last();

$post = new Post('Hello world!', 'Lorem ispum dolor sit amet...');
$postRepository->save($post);
```
