<?php

namespace flexycms\FlexyArticlesBundle\Entity;

use flexycms\FlexyArticlesBundle\Repository\ArticleTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleTypeRepository::class)
 */
class ArticleType
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
     * @ORM\Column(type="boolean")
     */
    private $hasSEO;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasImage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasImageAlbum;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publishedDefault;

    /**
     * @ORM\Column(type="smallint")
     */
    private $descriptionType;

    /**
     * @ORM\Column(type="smallint")
     */
    private $textType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasRemark;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $parameters;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasRubric;

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

    public function getHasSEO(): ?bool
    {
        return $this->hasSEO;
    }

    public function setHasSEO(bool $hasSEO): self
    {
        $this->hasSEO = $hasSEO;

        return $this;
    }

    public function getHasImage(): ?bool
    {
        return $this->hasImage;
    }

    public function setHasImage(bool $hasImage): self
    {
        $this->hasImage = $hasImage;

        return $this;
    }

    public function getHasImageAlbum(): ?bool
    {
        return $this->hasImageAlbum;
    }

    public function setHasImageAlbum(bool $hasImageAlbum): self
    {
        $this->hasImageAlbum = $hasImageAlbum;

        return $this;
    }

    public function getPublishedDefault(): ?bool
    {
        return $this->publishedDefault;
    }

    public function setPublishedDefault(bool $publishedDefault): self
    {
        $this->publishedDefault = $publishedDefault;

        return $this;
    }

    public function getDescriptionType(): ?int
    {
        return $this->descriptionType;
    }

    public function setDescriptionType(int $descriptionType): self
    {
        $this->descriptionType = $descriptionType;

        return $this;
    }

    public function getTextType(): ?int
    {
        return $this->textType;
    }

    public function setTextType(int $textType): self
    {
        $this->textType = $textType;

        return $this;
    }

    public function getHasRemark(): ?bool
    {
        return $this->hasRemark;
    }

    public function setHasRemark(bool $hasRemark): self
    {
        $this->hasRemark = $hasRemark;

        return $this;
    }

    public function getHasDate(): ?bool
    {
        return $this->hasDate;
    }

    public function setHasDate(bool $hasDate): self
    {
        $this->hasDate = $hasDate;

        return $this;
    }

    public function getParameters(): array
    {
        $parametersArray = array();
        try {
            $arr = json_decode($this->parameters, true);
            foreach($arr as $item)
            {
                $ap = new ArticleParameter($item);
                $ap->setValue($ap->getDefault($ap->getType()));
                $parametersArray[$item['code']] = $ap;
            }
        } catch(\Exception $ex) {
            $parametersArray = array();
        }
        return $parametersArray;
    }

    public function setParameters(array $value): self
    {
        $parameters = array();
        foreach($value as $item)
        {
            if ($item->getCode() == '') continue;
            $parameters[$item->getCode()] = $item->toArray();
        }

        $this->parameters = json_encode($parameters);
        return $this;
    }

    public function getParameter(string $code): ?ArticleParameter
    {
        $p = $this->getParameters();
        if (isset($p[$code])) return $p[$code];
        return null;
    }

    public function setParameter($code, ArticleParameter $parameter): self
    {
        $p = $this->getParameters();
        $p[$code] = $parameter;
        $this->setParameters($p);
        return $this;
    }

    public function deleteParameter($code): self
    {
        $p = $this->getParameters();
        unset ($p[$code]);
        $this->setParameters($p);
        return $this;
    }

    public function getHasRubric(): ?bool
    {
        return $this->hasRubric;
    }

    public function setHasRubric(bool $hasRubric): self
    {
        $this->hasRubric = $hasRubric;

        return $this;
    }

}
