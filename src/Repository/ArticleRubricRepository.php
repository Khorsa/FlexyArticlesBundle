<?php

namespace flexycms\FlexyArticlesBundle\Repository;

use flexycms\FlexyArticlesBundle\Entity\ArticleRubric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleRubric|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleRubric|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleRubric[]    findAll()
 * @method ArticleRubric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRubricRepository extends ServiceEntityRepository
{
    private $dataManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $dataManager)
    {
        $this->dataManager = $dataManager;
        parent::__construct($registry, ArticleRubric::class);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return parent::findAll();
    }

    public function getBySection($section): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->andWhere("t.section = :section");
        $qb->setParameter("section", $section);

        $qb->orderBy('t.sort', 'ASC');


        return $qb->getQuery()->execute();
    }

    /**
     * @return array
     */
    public function getRubricSectionList()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c.section')
            ->distinct(true);
        return $qb->getQuery()->execute();
    }

    /**
     * @param string $searchString
     * @param array|null $order
     * @param array|null $limit
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getBySearch(string $searchString, ?array $order, ?array  $limit): array
    {
        $qb = $this->createQueryBuilder('c');

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("c.name", $searchString)));
        }
        if (is_array($order)) $qb->orderBy('c.'.$order[0], $order[1]);
        if (is_array($limit)) {
            if (is_numeric($limit[0])) $qb->setFirstResult((int)($limit[0]));
            if (is_numeric($limit[1])) $qb->setMaxResults((int)($limit[1]));
        }
        return $qb->getQuery()->execute();
    }

    /**
     * @param string $searchString
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function countBySearch(string $searchString): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');

        if (trim($searchString)) {
            $qb->addCriteria(Criteria::create()->andWhere(Criteria::expr()->contains("c.name", $searchString)));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }


    public function getOne(int $objectId): ArticleRubric
    {
        return parent::find($objectId);
    }

    public function create(ArticleRubric $rubric): ArticleRubric
    {
        $rubric->setCreateAt();
        $rubric->setUpdateAt();
        $this->dataManager->persist($rubric);
        $this->dataManager->flush();

        return $rubric;
    }

    public function update(ArticleRubric $rubric): ArticleRubric
    {
        $rubric->setUpdateAt();
        $this->dataManager->flush();

        return $rubric;
    }

    public function delete(ArticleRubric $rubric)
    {
        $this->dataManager->remove($rubric);
        $this->dataManager->flush();
    }
}


