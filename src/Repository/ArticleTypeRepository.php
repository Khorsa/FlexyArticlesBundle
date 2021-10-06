<?php

namespace flexycms\FlexyArticlesBundle\Repository;

use flexycms\FlexyArticlesBundle\Entity\ArticleType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleType[]    findAll()
 * @method ArticleType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleTypeRepository extends ServiceEntityRepository
{
    private $dataManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $dataManager)
    {
        $this->dataManager = $dataManager;

        parent::__construct($registry, ArticleType::class);
    }

    public function getAll(): array
    {
        return parent::findAll();
    }

    public function getOne(int $objectId): ArticleType
    {
        $type = parent::find($objectId);
        return $type;
    }

    public function delete(ArticleType $type)
    {
        $this->dataManager->remove($type);
        $this->dataManager->flush();
    }

    public function create(ArticleType $category): ArticleType
    {
        $this->dataManager->persist($category);
        $this->dataManager->flush();

        return $category;
    }

    public function update(ArticleType $category): ArticleType
    {
        $this->dataManager->flush();

        return $category;
    }
}
