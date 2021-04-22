<?php

namespace flexycms\FlexyArticlesBundle\Entity;

use flexycms\FlexyArticlesBundle\Repository\ArticleRubricRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRubricRepository::class)
 */
class ArticleRubric
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SEOKeywords;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SEODescription;

    /**
     * @ORM\ManyToMany(targetEntity=Article::class, mappedBy="rubric")
     */
    private $articles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SEOTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $section;


    /**
     *
     * 0 - слева вверху
     * 1 - посередине вверху
     * 2 - справа вверху
     * 3 - слева посередине
     * 4 - посередине
     * 5 - справа посередине
     * 6 - слева внизу
     * 7 - посередине внизу
     * 8 - справа внизу
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $textPosition;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $textColor;







    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }


    public function __toString()
    {
        return $this->name;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt = null): self
    {
        if ($createAt === null) $createAt = new \DateTime();
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt = null): self
    {
        if ($updateAt === null) $updateAt = new \DateTime();
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getSEOKeywords(): ?string
    {
        return $this->SEOKeywords;
    }

    public function setSEOKeywords(string $SEOKeywords): self
    {
        $this->SEOKeywords = $SEOKeywords;

        return $this;
    }

    public function getSEODescription(): ?string
    {
        return $this->SEODescription;
    }

    public function setSEODescription(string $SEODescription): self
    {
        $this->SEODescription = $SEODescription;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addRubric($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeRubric($this);
        }

        return $this;
    }

    public function getSEOTitle(): ?string
    {
        return $this->SEOTitle;
    }

    public function setSEOTitle(string $SEOTitle): self
    {
        $this->SEOTitle = $SEOTitle;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(?string $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTextPosition()
    {
        return $this->textPosition;
    }

    /**
     * @param mixed $textPosition
     */
    public function setTextPosition($textPosition): void
    {
        $this->textPosition = $textPosition;
    }

    /**
     * @return mixed
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * @param mixed $textColor
     */
    public function setTextColor($textColor): void
    {
        $this->textColor = $textColor;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort): void
    {
        $this->sort = $sort;
    }
}
