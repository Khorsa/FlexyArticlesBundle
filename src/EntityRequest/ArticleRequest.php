<?php


namespace flexycms\FlexyArticlesBundle\EntityRequest;


use flexycms\FlexyArticlesBundle\Entity\Article;
use flexycms\FlexyArticlesBundle\Entity\ArticleCategory;
use flexycms\FlexyArticlesBundle\Repository\ArticleRepository;
use flexycms\FlexyArticlesBundle\Repository\ArticleTypeRepository;
use flexycms\FlexyFilemanagerBundle\Service\ImageManagerService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use flexycms\FlexyAdminFrameBundle\EntityRequest\EntityRequestInterface;

/**
 * Промежуточный класс для создания формы редактирования статьи
 * Class ArticleRequest
 */
class ArticleRequest implements EntityRequestInterface
{
    private $fileManager;
    private $repository;

    public $title = '';
    public $SEOTitle = '';
    public $SEOKeywords = '';
    public $SEODescription = '';
    public $content = '';
    public $description = '';
    public $imageFilename = '';
    public $createAt;
    public $updateAt;
    public $isPublished = false;
    public $parent;
    public $articleType;
    public $code;

    public $imageAlt = '';
    public $imageTitle = '';

    private $article;
    private $image;

    public $imageArray;

    public $rubrics;


    public $remark;
    public $dateAt;

    public $parameters;

    public $sort = 10;

    private $isNew;
    private $typeRepository;


    private $backPath;


    public function __construct(ImageManagerService $fileManager, ArticleRepository $articleRepository, ArticleTypeRepository $typeRepository)
    {
        $this->fileManager = $fileManager;
        $this->repository = $articleRepository;
        $this->typeRepository = $typeRepository;

        $this->backPath = '';
    }


    /**
     * @return Article
     */
    public function get(): Article
    {
        return $this->article;
    }





    public function createForParent(ArticleCategory $parent)
    {
        $this->create();

        if (!$parent) throw new \Exception("Заведите хотя бы одну категорию статей");
        $this->articleType = $parent->getDefaultArticleType();

        //Определяем из типа значения по-умолчанию
        $this->isPublished = $this->articleType->getPublishedDefault();
        $this->parent = $parent;
    }

    //Создание новой статьи
    public function create()
    {
        // Проверяем наличие хотя бы одного ArticleType
        if ($this->typeRepository->count([]) == 0) throw new \Exception("Заведите хотя бы один тип статей");

        $this->article = new Article();

        // Поля статьи
        $this->title = '';
        $this->SEOTitle = '';
        $this->SEOKeywords = '';
        $this->SEODescription = '';
        $this->content = '';
        $this->description = '';
        $this->imageFilename = '';
        $this->createAt = new \DateTime();
        $this->updateAt = new \DateTime();
        $this->isPublished = false;
        $this->code = substr(md5(uniqid()), 0, 10);

        $this->rubrics = array();


        $this->parameters = [];
        $this->remark = '';
        $this->dateAt = new \DateTime();

        $this->sort = 10;

        //Поля изображений фотоальбома
        $this->imageArray = array();

        //Поля основного изображения
        $this->image = null;
        $this->isNew = true;
    }

    //Получение данных существующей статьи
    public function load($articleId)
    {
        $this->article = $this->repository->getOne($articleId);

        if (!$this->article) throw new \Exception("Статья {$articleId} не найдена!");

        $this->isNew = false;

        // Поля статьи
        $this->title = $this->article->getTitle();
        $this->SEOTitle = $this->article->getSEOTitle();
        $this->SEOKeywords = $this->article->getSEOKeywords();
        $this->SEODescription = $this->article->getSEODescription();
        $this->content = $this->article->getContent();
        $this->description = $this->article->getDescription();
        $this->imageFilename = $this->article->getImageFilename();
        $this->createAt = $this->article->getCreateAt();
        $this->updateAt = $this->article->getUpdateAt();
        $this->isPublished = $this->article->getIsPublished();
        $this->articleType = $this->article->getParent()->getDefaultArticleType();
        $this->parent = $this->article->getParent();
        $this->code = $this->article->getCode();
        $this->parameters = $this->article->getParameters();
        $this->rubrics = $this->article->getRubric();
        $this->sort = $this->article->getSort();


        $this->remark = $this->article->getRemark();
        $this->dateAt = $this->article->getDateAt();


        //Поля изображений фотоальбома
        $this->imageArray = array();
        foreach($this->article->getImageArray() as $imageFilename)
        {
            $image = $this->fileManager->getFile($imageFilename);
            if ($image)
            {
                $imageAlt = $image->getMetaDataValue('alt');
                $imageTitle = $image->getMetaDataValue('title');
                $this->imageArray[] = ['image' => $image, 'alt' => $imageAlt, 'title' => $imageTitle];
            }
        }

        //Поля основного изображения
        $image = $this->fileManager->getFile($this->article->getImageFilename());
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
            $oldFile = $this->fileManager->getFile($this->article->getImageFilename());
            if ($oldFile) $this->fileManager->delete($oldFile);
            $this->article->setImageFilename($file->getName());

            $this->image = $file;
        }
    }



    public function addAlbumImage(UploadedFile $uploadedFile)
    {
        $file = $this->fileManager->upload($uploadedFile);
        if ($file)
        {
            $this->imageArray[] = ['image' => $file, 'alt' => $file->getMetaDataValue('alt'), 'title' => $file->getMetaDataValue('title')];
        }
    }









    public function save()
    {
        // Поля статьи
        $this->article->setTitle($this->title);
        $this->article->setSEOTitle($this->SEOTitle);
        $this->article->setSEOKeywords($this->SEOKeywords);
        $this->article->setSEODescription($this->SEODescription);
        $this->article->setContent($this->content);
        $this->article->setDescription($this->description);
        $this->article->setCreateAt($this->createAt);
        $this->article->setUpdateAt($this->updateAt);
        $this->article->setIsPublished($this->isPublished);
        $this->article->setParent($this->parent);
        $this->article->setArticleType($this->articleType);
        $this->article->setCode($this->code);
        $this->article->setSort($this->sort);

        //Добавляем новые
        foreach($this->rubrics as $rubric) {
            $this->article->addRubric($rubric);
        }

        //Удаляем ненужные
        $currentRubrics = $this->article->getRubric();
        foreach ($currentRubrics as $currentRubric) {

            //dump($this->rubrics);

//            if (!in_array($currentRubric, $this->rubrics)) $this->article->removeRubric($currentRubric);
        }

        $this->article->setParameters($this->parameters);

        $this->article->setRemark($this->remark);
        $this->article->setDateAt($this->dateAt);


        //Поля основного изображения
        if ($this->image)
        {
            $this->image->setMetaDataValue('alt', $this->imageAlt);
            $this->image->setMetaDataValue('title', $this->imageTitle);
            $this->fileManager->update($this->image);
        }

        //Поля изображений фотоальбома
        $imageArray = array();
        foreach($this->imageArray as $imageItem)
        {
            $image = $imageItem['image'];
            if ($image)
            {
                $image->setMetaDataValue('alt', $imageItem['alt']);
                $image->setMetaDataValue('title', $imageItem['title']);
            }
            $imageArray[] = $image->getName();
        }
        $this->article->setImageArray($imageArray);

        if ($this->isNew) {
            $this->repository->setCreateArticle($this->article);
        } else {
            $this->repository->setUpdateArticle($this->article);
        }
    }


    public function getFormModifiers(): array
    {
        $modifiers = array();

        $modifiers['hasImage'] = $this->articleType->getHasImage();
        $modifiers['hasImageAlbum'] = $this->articleType->getHasImageAlbum();
        $modifiers['hasSEO'] = $this->articleType->getHasSEO();

        $modifiers['descriptionType'] = $this->articleType->getDescriptionType();
        $modifiers['textType'] = $this->articleType->getTextType();

        $modifiers['hasRubrics'] = $this->articleType->getHasRubric();
        $modifiers['hasRemark'] = $this->articleType->getHasRemark();
        $modifiers['hasDate'] = $this->articleType->getHasDate();
        //$modifiers['parameters'] = $this->articleType->getParameters();

        $modifiers['hasParameters'] = true;
        if (count($this->articleType->getParameters()) == 0) $modifiers['hasParameters'] = false;




        return $modifiers;
    }





/*
    public function __get($name)
    {
        $prefix = 'parameters__';

        if (strpos($name, $prefix) === 0 && strlen($name) >= strlen($prefix))
        {
            $code = substr($name, strlen($prefix));
            $res =  $this->article->getParameterValue($code);
            return $res;
        }
        throw new \Exception("Field {$name} not found in class ". __CLASS__ ."");
    }

    public function __set($name, $value)
    {
        $prefix = 'parameters__';
        if (strpos($name, $prefix) === 0 && strlen($name) >= strlen($prefix))
        {
            $code = substr($name, strlen($prefix));
            $this->article->setParameterValue($code, $value);
            return;
        }
        throw new \Exception("Field {$name} not found in class ". __CLASS__ .")");

    }
*/
    /**
     * @return string
     */
    public function getBackPath(): string
    {
        return $this->backPath;
    }

    /**
     * @param string $backPath
     * @return $this
     */
    public function setBackPath(string $backPath): self
    {
        $this->backPath = $backPath;
        return $this;
    }


}