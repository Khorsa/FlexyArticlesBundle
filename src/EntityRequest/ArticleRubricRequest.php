<?php


namespace flexycms\FlexyArticlesBundle\EntityRequest;


use flexycms\FlexyArticlesBundle\Entity\ArticleRubric;
use flexycms\FlexyArticlesBundle\Repository\ArticleRubricRepository;
use flexycms\FlexyFilemanagerBundle\Service\ImageManagerService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use flexycms\FlexyAdminFrameBundle\EntityRequest\EntityRequestInterface;

class ArticleRubricRequest implements EntityRequestInterface
{
    private $fileManager;
    private $repository;

    public $name = '';
    public $createAt;
    public $updateAt;
    public $imageFilename;
    public $SEOTitle = '';
    public $SEOKeywords = '';
    public $SEODescription = '';
    public $code;
    public $section;
    public $sort;

    public $textPosition;
    public $textColor;

    public $imageAlt = '';
    public $imageTitle = '';

    /**
     * @var ArticleRubric
     */
    private $rubric;

    private $image;

    private $isNew;


    public function __construct(ImageManagerService $fileManager, ArticleRubricRepository $articleRubricRepository)
    {
        $this->fileManager = $fileManager;
        $this->repository = $articleRubricRepository;
    }

    /**
     * @return ArticleRubric
     */
    public function get(): ArticleRubric
    {
        return $this->rubric;
    }



    public function create()
    {
        $this->rubric = new ArticleRubric();

        // Поля рубрики
        $this->name = '';
        $this->SEOTitle = '';
        $this->SEOKeywords = '';
        $this->SEODescription = '';
        $this->imageFilename = '';
        $this->section = '';
        $this->textPosition = 0;
        $this->sort = 10;
        $this->textColor = '000000';
        $this->createAt = new \DateTime();
        $this->updateAt = new \DateTime();
        $this->code = substr(md5(uniqid()), 0, 10);

        //Поля основного изображения
        $this->image = null;

        $this->isNew = true;
    }

    public function load($rubricId)
    {
        $this->rubric = $this->repository->getOne($rubricId);

        if (!$this->rubric) throw new \Exception("Рубрика статей {$rubricId} не найдена!");

        $this->isNew = false;

        //Поля категории
        $this->name = $this->rubric->getName();
        $this->SEOTitle = $this->rubric->getSEOTitle();
        $this->SEOKeywords = $this->rubric->getSEOKeywords();
        $this->SEODescription = $this->rubric->getSEODescription();
        $this->imageFilename = $this->rubric->getImageFilename();
        $this->createAt = $this->rubric->getCreateAt();
        $this->updateAt = $this->rubric->getUpdateAt();
        $this->code = $this->rubric->getCode();
        $this->sort = $this->rubric->getSort();
        $this->section = $this->rubric->getSection();
        $this->textColor = $this->rubric->getTextColor();
        $this->textPosition = $this->rubric->getTextPosition();

        //Поля основного изображения
        $image = $this->fileManager->getFile($this->rubric->getImageFilename());
        $this->image = $image;

        if ($image)
        {
            $this->imageAlt = $image->getMetaDataValue('alt');
            $this->imageTitle = $image->getMetaDataValue('title');
        }
    }

    public function uploadImage(UploadedFile $uploadedFile)
    {
        $file = $this->fileManager->upload($uploadedFile);

        if ($file)
        {
            // Удаляем старый файл, привязанный к статье
            $oldFile = $this->fileManager->getFile($this->rubric->getImageFilename());
            if ($oldFile) $this->fileManager->delete($oldFile);
            $this->rubric->setImageFilename($file->getName());

            $this->image = $file;
        }
    }


    public function save()
    {
        // Поля категории
        $this->rubric->setName($this->name);
        $this->rubric->setSEOTitle($this->SEOTitle);
        $this->rubric->setSEOKeywords($this->SEOKeywords);
        $this->rubric->setSEODescription($this->SEODescription);
        $this->rubric->setCreateAt($this->createAt);
        $this->rubric->setUpdateAt($this->updateAt);
        $this->rubric->setCode($this->code);
        $this->rubric->setSort($this->sort);
        $this->rubric->setSection($this->section);
        $this->rubric->setTextColor($this->textColor);
        $this->rubric->setTextPosition($this->textPosition);

        //Поля основного изображения
        if ($this->image)
        {
            $this->image->setMetaDataValue('alt', $this->imageAlt);
            $this->image->setMetaDataValue('title', $this->imageTitle);
            $this->fileManager->update($this->image);
        }

        if ($this->isNew) {
            $this->repository->create($this->rubric);
        } else {
            $this->repository->update($this->rubric);
        }
    }

    public function getFormModifiers(): array
    {
        $result = array();

        return $result;
    }
}