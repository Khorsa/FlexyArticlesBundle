<?php


namespace flexycms\FlexyArticlesBundle\EntityRequest;


use flexycms\FlexyArticlesBundle\Entity\ArticleCategory;
use flexycms\FlexyArticlesBundle\Repository\ArticleCategoryRepository;
use flexycms\FlexyArticlesBundle\Repository\ArticleTypeRepository;
use flexycms\FlexyFilemanagerBundle\Service\ImageManagerService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use flexycms\FlexyAdminFrameBundle\EntityRequest\EntityRequestInterface;

class ArticleCategoryRequest implements EntityRequestInterface
{
    private $fileManager;
    private $repository;
    private $typeRepository;

    public $name = '';
    public $parent = null;
    public $createAt;
    public $updateAt;
    public $imageFilename;
    public $SEOTitle = '';
    public $SEOKeywords = '';
    public $SEODescription = '';
    public $defaultArticleType;
    public $code;
    public $showInMenu = false;

    public $imageAlt = '';
    public $imageTitle = '';

    private $category;
    private $image;

    private $isNew;

    public function __construct(ImageManagerService $fileManager, ArticleCategoryRepository $articleCategoryRepository, ArticleTypeRepository $typeRepository)
    {
        $this->fileManager = $fileManager;
        $this->repository = $articleCategoryRepository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * @return ArticleCategory
     */
    public function get(): ArticleCategory
    {
        return $this->category;
    }


    public function createForParent(ArticleCategory $parent)
    {
        $this->create();

        $this->defaultArticleType = $parent->getDefaultArticleType();
        $this->parent = $parent;
    }

    public function create()
    {
        // Проверяем наличие хотя бы одного ArticleType
        if ($this->typeRepository->count([]) == 0) throw new \Exception("Заведите хотя бы один тип статей");

        $this->category = new ArticleCategory();

        // Выбираем первый попавшийся тип
        $type = $this->typeRepository->findOneBy([]);

        // Поля категории
        $this->name = '';
        $this->parent = null;
        $this->SEOTitle = '';
        $this->SEOKeywords = '';
        $this->SEODescription = '';
        $this->imageFilename = '';
        $this->createAt = new \DateTime();
        $this->updateAt = new \DateTime();
        $this->defaultArticleType = $type;
        $this->code = substr(md5(uniqid()), 0, 10);
        $this->showInMenu = false;

        //Поля основного изображения
        $this->image = null;

        $this->isNew = true;
    }

    public function load($categoryId)
    {
        // Проверяем наличие хотя бы одного ArticleType
        if ($this->typeRepository->count([]) == 0) throw new \Exception("Заведите хотя бы один тип статей");

        $this->category = $this->repository->getOne($categoryId);

        if (!$this->category) throw new \Exception("Категория статей {$categoryId} не найдена!");

        $this->isNew = false;

        //Поля категории
        $this->name = $this->category->getName();
        $this->parent = $this->category->getParent();
        $this->SEOTitle = $this->category->getSEOTitle();
        $this->SEOKeywords = $this->category->getSEOKeywords();
        $this->SEODescription = $this->category->getSEODescription();
        $this->imageFilename = $this->category->getImageFilename();
        $this->createAt = $this->category->getCreateAt();
        $this->updateAt = $this->category->getUpdateAt();
        $this->defaultArticleType = $this->category->getDefaultArticleType();
        $this->code = $this->category->getCode();
        $this->showInMenu = $this->category->getShowInMenu();

        //Поля основного изображения
        $image = $this->fileManager->getFile($this->category->getImageFilename());
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
            $oldFile = $this->fileManager->getFile($this->category->getImageFilename());
            if ($oldFile) $this->fileManager->delete($oldFile);
            $this->category->setImageFilename($file->getName());

            $this->image = $file;
        }
    }


    public function save()
    {
        // Поля категории
        $this->category->setName($this->name);
        $this->category->setParent($this->parent);
        $this->category->setSEOTitle($this->SEOTitle);
        $this->category->setSEOKeywords($this->SEOKeywords);
        $this->category->setSEODescription($this->SEODescription);
        $this->category->setCreateAt($this->createAt);
        $this->category->setUpdateAt($this->updateAt);
        $this->category->setDefaultArticleType($this->defaultArticleType);
        $this->category->setCode($this->code);
        $this->category->setShowInMenu($this->showInMenu);

        //Поля основного изображения
        if ($this->image)
        {
            $this->image->setMetaDataValue('alt', $this->imageAlt);
            $this->image->setMetaDataValue('title', $this->imageTitle);
            $this->fileManager->update($this->image);
        }

        if ($this->isNew) {
            $this->repository->create($this->category);
        } else {
            $this->repository->update($this->category);
        }
    }

    public function getFormModifiers(): array
    {
        $result = array();

        return $result;
    }



}