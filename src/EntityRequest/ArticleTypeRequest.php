<?php


namespace flexycms\FlexyArticlesBundle\EntityRequest;


use flexycms\FlexyArticlesBundle\Entity\ArticleType;
use flexycms\FlexyAdminFrameBundle\EntityRequest\EntityRequestInterface;
use flexycms\FlexyArticlesBundle\Repository\ArticleTypeRepository;


class ArticleTypeRequest implements EntityRequestInterface
{
    private $repository;

    public $name = '';
    public $hasSEO = 1;
    public $hasImage = 1;
    public $hasImageAlbum = 1;
    public $publishedDefault = 1;
    public $descriptionType = 1;
    public $textType = 1;

    public $hasRubric = 1;
    public $hasRemark = 1;
    public $hasDate = 1;
    public $parameters = array();

    private $type;
    private $isNew;


    public function __construct(ArticleTypeRepository $articleTypeRepository)
    {
        $this->repository = $articleTypeRepository;
    }

    /**
     * @return ArticleType
     */
    public function get(): ArticleType
    {
        return $this->type;
    }


    public function create()
    {
        $this->type = new ArticleType();

        // Поля типа
        $this->name = '';
        $this->hasSEO = true;
        $this->hasImage = true;
        $this->hasImageAlbum = true;
        $this->publishedDefault = true;
        $this->descriptionType = 1;
        $this->textType = 1;

        $this->hasRubric = true;
        $this->hasRemark = true;
        $this->hasDate = true;
        $this->parameters = array();

        $this->isNew = true;
    }

    public function load($typeId)
    {
        $this->type = $this->repository->getOne($typeId);

        if (!$this->type) throw new \Exception("Тип статей {$typeId} не найден!");

        $this->isNew = false;

        //Поля типа
        $this->name = $this->type->getName();
        $this->hasSEO = $this->type->getHasSEO();
        $this->hasImage = $this->type->getHasImage();
        $this->hasImageAlbum = $this->type->getHasImageAlbum();
        $this->publishedDefault = $this->type->getPublishedDefault();
        $this->descriptionType = $this->type->getDescriptionType();
        $this->textType = $this->type->getTextType();

        $this->hasRubric = $this->type->getHasRubric();
        $this->hasRemark = $this->type->getHasRemark();
        $this->hasDate = $this->type->getHasDate();
        $this->parameters = $this->type->getParameters();

    }

    public function save()
    {
        // Поля типа
        $this->type->setName($this->name);
        $this->type->setHasSeo($this->hasSEO);
        $this->type->setHasImage($this->hasImage);
        $this->type->setHasImageAlbum($this->hasImageAlbum);
        $this->type->setPublishedDefault($this->publishedDefault);
        $this->type->setDescriptionType($this->descriptionType);
        $this->type->setTextType($this->textType);

        $this->type->setHasRubric($this->hasRubric);
        $this->type->setHasRemark($this->hasRemark);
        $this->type->setHasDate($this->hasDate);
        $this->type->setParameters($this->parameters);

        if ($this->isNew) {
            $this->repository->create($this->type);
        } else {
            $this->repository->update($this->type);
        }
    }

    public function getFormModifiers(): array
    {
        $result = array();
        return $result;
    }



}