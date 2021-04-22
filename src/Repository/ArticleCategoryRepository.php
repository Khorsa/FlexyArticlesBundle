<?php

namespace flexycms\FlexyArticlesBundle\Repository;

use flexycms\FlexyArticlesBundle\Entity\ArticleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use flexycms\FlexyFilemanagerBundle\Service\ImageManagerService;

/**
 * @method ArticleCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleCategory[]    findAll()
 * @method ArticleCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleCategoryRepository extends ServiceEntityRepository
{
    private $dataManager;
    private $fileManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $dataManager, ImageManagerService $fileManager)
    {
        $this->dataManager = $dataManager;
        $this->fileManager = $fileManager;
        parent::__construct($registry, ArticleCategory::class);
    }

    public function getAll(): array
    {
        return parent::findAll();
    }

    /**
     * @param int|null $parentId
     * @param string $searchString
     * @param array|null $order
     * @param array|null $limit
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getBySearch(?int $parentId, string $searchString, ?array $order, ?array  $limit): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('c.parent')));
        } else {
          $qb->andWhere("c.parent = {$parentId}");
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("c.name", $searchString)));
        }
        if (is_array($order)) $qb->orderBy('c.'.$order[0], $order[1]);
        $qb->setFirstResult($limit[0]);
        $qb->setMaxResults($limit[1]);
        return $qb->getQuery()->execute();
    }


    /**
     * @param int|null $parentId
     * @param string $searchString
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function countBySearch(?int $parentId, string $searchString): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('c.parent')));
        } else {
            $qb->andWhere("c.parent = {$parentId}");
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("c.name", $searchString)));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int|null $parentId
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function countAll(?int $parentId): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('c.parent')));
        } else {
            $qb->andWhere("c.parent = {$parentId}");
        }
        return $qb->getQuery()->getSingleScalarResult();
    }



    public function getOne(int $objectId): ArticleCategory
    {
        return parent::find($objectId);
    }

    public function create(ArticleCategory $category): ArticleCategory
    {
        $category->setCreateAt();
        $category->setUpdateAt();
        $this->dataManager->persist($category);
        $this->dataManager->flush();

        return $category;
    }

    public function update(ArticleCategory $category): ArticleCategory
    {
        $category->setUpdateAt();
        $this->dataManager->flush();

        return $category;
    }

    public function delete(ArticleCategory $category)
    {

        $subs = $this->createQueryBuilder('c')
            ->andWhere("c.parent = :val")
            ->setParameter('val', $category->getId())
            ->getQuery()
            ->execute();

        foreach($subs as $sub) {
            $this->delete($sub);
        }

        $this->dataManager->remove($category);
        $this->dataManager->flush();
    }

    /**
      * @return ArticleCategory[] Returns an array of ArticleCategory objects
      */

    public function getForMenu(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.showInMenu = 1')
            ->orderBy('a.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



    // /**
    //  * @return ArticleCategory[] Returns an array of ArticleCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleCategory
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
