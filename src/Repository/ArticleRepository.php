<?php

namespace flexycms\FlexyArticlesBundle\Repository;

use flexycms\FlexyArticlesBundle\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use flexycms\FlexyFilemanagerBundle\Service\ImageManagerService;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $dataManager;
    private $fileManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $dataManager, ImageManagerService $fileManager)
    {
        $this->dataManager = $dataManager;
        $this->fileManager = $fileManager;
        parent::__construct($registry, Article::class);
    }


    /**
     * @param int|null $parentId
     * @param string $searchString
     * @param array|null $order
     * @param array|null $limit
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getSearchByCategory(?int $parentId, string $searchString, ?array $order = null, ?array  $limit = null): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('t.parent')));
        } else {
            $qb->andWhere("t.parent = {$parentId}");
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("t.title", $searchString)));
        }
        if (is_array($order)) $qb->orderBy('t.'.$order[0], $order[1]);
        if (is_array($limit)) {
            if (is_numeric($limit[0])) $qb->setFirstResult((int)($limit[0]));
            if (is_numeric($limit[1])) $qb->setMaxResults((int)($limit[1]));
        }
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
    public function countSearchByCategory(?int $parentId, string $searchString): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('t.parent')));
        } else {
            $qb->andWhere("t.parent = {$parentId}");
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("t.title", $searchString)));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }





    /**
     * @param int|null $rubricId
     * @param string $searchString
     * @param array|null $order
     * @param array|null $limit
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getSearchByRubric(?int $rubricId, string $searchString, ?array $order, ?array  $limit): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($rubricId !== null) {
            $qb->innerJoin('t.rubric', 'r')
                ->andWhere("r.id = :rubricId")
                ->setParameter('rubricId', $rubricId);
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("t.title", $searchString)));
        }
        if (is_array($order)) $qb->orderBy('t.'.$order[0], $order[1]);
        if (is_array($limit)) {
            if (is_numeric($limit[0])) $qb->setFirstResult((int)($limit[0]));
            if (is_numeric($limit[1])) $qb->setMaxResults((int)($limit[1]));
        }

        return $qb->getQuery()->execute();
    }


    /**
     * @param int|null $rubricId
     * @param string $searchString
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function countSearchByRubric(?int $rubricId, string $searchString): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        if ($rubricId !== null) {
            $qb->innerJoin('t.rubric', 'r')
                ->andWhere("r.id = :rubricId")
                ->setParameter('rubricId', $rubricId);
        }

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("t.title", $searchString)));
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
    public function countAllByCategory(?int $parentId = null): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        if ($parentId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('t.parent')));
        } else {
            $qb->andWhere("t.parent = {$parentId}");
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
    public function countAllByRubric(?int $rubricId = null): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        if ($rubricId === null) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->isNull('t.parent')));
        } else {
            $qb->andWhere("{$rubricId} in t.rubrics");
        }
        return $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * @param Article $article
     * @param string $fileName
     */
    public function deletePhoto(Article $article, string $fileName)
    {
        $oldImageArray = $article->getImageArray();
        $imageArray = array();
        foreach($oldImageArray as $image) {
            if ($image == $fileName) continue;
            $imageArray[] = $image;
        }

        // ?? ?????????????? ?????? ????????
        $file = $this->fileManager->findByName($fileName);
        $this->fileManager->delete($file);

        $article->setImageArray($imageArray);
        $this->setUpdateArticle($article);
    }


    public function getAll(): array
    {
        return parent::findAll();
    }

    public function getOne(int $objectId): Article
    {
        $article = parent::find($objectId);
        return $article;
    }

    public function setCreateArticle(Article $article): Article
    {
        $article->setCreateAt();
        $article->setUpdateAt();
        $this->dataManager->persist($article);
        $this->dataManager->flush();

        return $article;
    }

    public function setUpdateArticle(Article $article): Article
    {

        $article->setUpdateAt();
        $this->dataManager->flush();

        return $article;
    }

    public function setDeleteArticle(Article $article)
    {
        $this->dataManager->remove($article);
        $this->dataManager->flush();
    }
}
