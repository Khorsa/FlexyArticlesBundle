<?php

namespace flexycms\FlexyArticlesBundle\Entity;

use flexycms\FlexyArticlesBundle\Repository\ArticleCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use flexycms\FlexyFilemanagerBundle\Entity\FlexyFile;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryRepository::class)
 */
class ArticleCategory
{

    public function __construct()
    {
        $this->setCreateAt();
        $this->setUpdateAt();
        $this->articles = new ArrayCollection();
    }


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
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class)
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     */
    private $parent;

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
    private $SEOTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SEOKeywords;

    /**
     * @ORM\Column(type="string", length=1023)
     */
    private $SEODescription;


    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $defaultArticleType;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="parent", orphanRemoval=true)
     */
    private $articles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showInMenu;
    /**
     * @return mixed
     */
    public function getImage(): FlexyFile
    {
        if (!$this->image) return new FlexyFile();
        return $this->image;
    }

    /**
     * @param FlexyFile $image
     */
    public function setImage(FlexyFile $image): void
    {
        $this->image = $image;
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

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

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

    public function getDefaultArticleType(): ?ArticleType
    {
        return $this->defaultArticleType;
    }

    public function setDefaultArticleType(?ArticleType $defaultArticleType): self
    {
        $this->defaultArticleType = $defaultArticleType;

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
            $article->setParent($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getParent() === $this) {
                $article->setParent(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getShowInMenu(): ?bool
    {
        return $this->showInMenu;
    }

    public function setShowInMenu(bool $showInMenu): self
    {
        $this->showInMenu = $showInMenu;

        return $this;
    }
}
