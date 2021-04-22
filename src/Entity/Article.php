<?php

namespace flexycms\FlexyArticlesBundle\Entity;

use flexycms\FlexyArticlesBundle\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use flexycms\FlexyUtilsBundle\Utils\Image;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->setCreateAt();
        $this->setUpdateAt();
        $this->setIsPublished(0);
        $this->rubric = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $imageArray;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $SEOTitle;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $SEOKeywords;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $SEODescription;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFilename;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $articleType;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remark;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $parameters;

    /**
     * @ORM\ManyToMany(targetEntity=ArticleRubric::class, inversedBy="articles")
     * @JoinTable(name="article_article_rubric",
     *      joinColumns={@JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="article_rubric_id", referencedColumnName="id")}
     *      )
     */
    private $rubric;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished($isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @return string
     */
    public function getSEOTitle()
    {
        return $this->SEOTitle;
    }

    /**
     * @param string $SEOTitle
     */
    public function setSEOTitle($SEOTitle): void
    {
        $this->SEOTitle = $SEOTitle;
    }

    /**
     * @return string
     */
    public function getSEOKeywords()
    {
        return $this->SEOKeywords;
    }

    /**
     * @param string $SEOKeywords
     */
    public function setSEOKeywords($SEOKeywords): void
    {
        $this->SEOKeywords = $SEOKeywords;
    }

    /**
     * @return string
     */
    public function getSEODescription()
    {
        return $this->SEODescription;
    }

    /**
     * @param string $SEODescription
     */
    public function setSEODescription($SEODescription): void
    {
        $this->SEODescription = $SEODescription;
    }

    public function getArticleType(): ?ArticleType
    {
        return $this->articleType;
    }

    public function setArticleType(?ArticleType $articleType): self
    {
        $this->articleType = $articleType;

        return $this;
    }

    public function getParent(): ?ArticleCategory
    {
        return $this->parent;
    }

    public function setParent(?ArticleCategory $parent): self
    {
        $this->parent = $parent;

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

    /**
     * @return mixed
     */
    public function getImageArray()
    {
        try {
            $decoded = json_decode($this->imageArray, true);
            if (is_array($decoded)) return $decoded;
        }
        catch(\Exception $ex) {}

        return array();
    }

    /**
     * @param mixed $imageArray
     */
    public function setImageArray($imageArray): void
    {
        $this->imageArray = json_encode($imageArray);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->dateAt;
    }

    public function setDateAt(?\DateTimeInterface $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getParameters(): array
    {
        $parametersArray = array();

        $parameters = $this->articleType->getParameters();
        foreach($parameters as $item)
        {
            $parametersArray[$item->getCode()] =$item;
        }

        try {
            $arr = json_decode($this->parameters, true);

            foreach($arr as $code => $value)
            {
                if (isset($parametersArray[$code])) $parametersArray[$code]->setValue($value);
            }
        } catch(\Exception $ex) {}

        return $parametersArray;
    }

    public function setParameters(array $values): self
    {
        $parameters = array();
        foreach($values as $value)
        {
            if ($value->getCode() == '') continue;
            $parameters[$value->getCode()] = $value->getValue();
        }

        $this->parameters = json_encode($parameters);
        return $this;
    }


    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setParameterValue(string $code, string $value): self
    {
        // Проверяем, есть ли такой параметр в типе статьи
        $p = $this->articleType->getParameter($code);
        if ($p instanceof ArticleParameter)
        {
            $params = $this->getParameters();
            $params[$code] = $value;
            $this->setParameters($params);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getParameterValue(string $name)
    {
        $params = $this->getParameters();
        if (isset($params[$name])) return $params[$name];
        return null;
    }

    /**
     * @return Collection|ArticleRubric[]
     */
    public function getRubric(): Collection
    {
        return $this->rubric;
    }

    public function addRubric(ArticleRubric $rubric): self
    {
        if (!$this->rubric->contains($rubric)) {
            $this->rubric[] = $rubric;
        }

        return $this;
    }

    public function removeRubric(ArticleRubric $rubric): self
    {
        $this->rubric->removeElement($rubric);

        return $this;
    }


    public function getSmallImage($width, $height, $type='cover') {

        $imagePath = "/public/uploads/embedded/" . $this->imageFilename;
        $imageFile = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

        $image = new Image();
        $image->load($imageFile);

        if ($type == 'cover') $image->cover($width, $height);
        if ($type == 'resize') $image->resize($width, $height);
        if ($type == 'contain') $image->contain($width, $height);

        $image->run();

        return $image;

    }





}
