<?php


namespace flexycms\FlexyArticlesBundle\Controller;

use flexycms\FlexyAdminFrameBundle\Controller\AdminBaseController;
use flexycms\FlexyArticlesBundle\EntityRequest\ArticleRequest;
use flexycms\FlexyArticlesBundle\Form\ArticleType;
use flexycms\FlexyArticlesBundle\Repository\ArticleCategoryRepository;
use flexycms\BreadcrumbsBundle\Utils\Breadcrumbs;
use flexycms\FlexyArticlesBundle\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AdminBaseController
{
    private $articleRepository;
    private $categoryRepository;

    public function __construct(ArticleRepository $articleRepository, ArticleCategoryRepository $categoryRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("admin/articles/create", name="admin_articles_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request, ArticleRequest $articleRequest)
    {
        $parentId = $request->get('parentid');
        $parent = $this->categoryRepository->getOne($parentId);

        try {
            $articleRequest->createForParent($parent);
        }
        catch (\Exception $ex)
        {
            $this->addFlash("danger", $ex->getMessage());
            return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parentId] );
        }

        $backPath = $request->get('backpath');
        if (!$backPath || strlen($backPath) < 1 || strpos($backPath, '/') !== 0) {
            $backPath = $this->generateUrl("admin_articlecategories", ['parentid' => $parentId]);
        }
        $articleRequest->setBackPath($backPath);

        $form = $this->createForm(ArticleType::class, $articleRequest);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $articleRequest->uploadImage($uploadedFile);
            }

            $uploadedFiles = $form->get('imageArray')->getData();
            if ($uploadedFiles) {
                foreach($uploadedFiles as $uploadedFile) {
                    $articleRequest->addAlbumImage($uploadedFile);
                }
            }

            $articleRequest->save();

            if ($form->get('apply')->isClicked() || $form->get('uploadfiles')->isClicked()) {
                return $this->redirectToRoute("admin_articles_edit", ['id' => $articleRequest->get()->getId()] );
            }


            return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parentId]);
        }


        $forRender = parent::renderDefault();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend($this->generateUrl("admin_articles_create"), 'Добавить статью');
        $breadcrumbs->prepend($this->generateUrl("admin_articlecategories", ['parentid' => $parentId]), 'Категории статей');
        $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;

        $forRender['title'] = "Добавление статьи";
        $forRender['form'] = $form->createView();
        $forRender['formModifiers'] = $articleRequest->getFormModifiers();

        $forRender['articleId'] = null;

        $forRender['backpath'] = $backPath;

        $forRender['imageArray'] = array();

        return $this->render("@FlexyArticles/articleform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articles/edit", name="admin_articles_edit")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, ArticleRequest $articleRequest)
    {
        $articleId = $request->get('id');
        $articleRequest->load($articleId);

        $backPath = $request->get('backpath');
        if (!$backPath || strlen($backPath) < 1 || strpos($backPath, '/') !== 0) {
            $backPath = $this->generateUrl("admin_articlecategories", ['parentid' => $articleRequest->get()->getParent()->getId()]);
        }

        $articleRequest->setBackPath($backPath);

        $form = $this->createForm(ArticleType::class, $articleRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $articleRequest->uploadImage($uploadedFile);
            }

            $uploadedFiles = $form->get('imageArray')->getData();
            if ($uploadedFiles) {
                foreach($uploadedFiles as $uploadedFile) {
                    $articleRequest->addAlbumImage($uploadedFile);
                }
            }

            $articleRequest->save();

            if ($form->get('apply')->isClicked() || $form->get('uploadfiles')->isClicked()) {
                return $this->redirectToRoute("admin_articles_edit", ['id' => $articleRequest->get()->getId(), 'backpath' => $backPath] );
            }

            return $this->redirect($backPath);
        }

        $forRender = parent::renderDefault();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend($this->generateUrl("admin_articles_create", ['parentid' => $articleId]), 'Редактировать статью');
        $breadcrumbs->prepend($this->generateUrl("admin_articlecategories", ['parentid' => $articleRequest->get()->getParent()->getId()]), 'Категории статей');
        $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;

        $forRender['title'] = "Редактирование статьи";
        $forRender['form'] = $form->createView();
        $forRender['formModifiers'] = $articleRequest->getFormModifiers();
        $forRender['articleId'] = $articleId;
        $forRender['imageArray'] = array();
        $forRender['backpath'] = $backPath;

        foreach($articleRequest->imageArray as $image) {
            $forRender['imageArray'][] = $image;
        }
        return $this->render("@FlexyArticles/articleform.html.twig", $forRender);
    }





    /**
     * @Route("admin/articles/delete", name="admin_articles_delete")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function delete(Request $request)
    {
        $article = $this->articleRepository->getOne($request->get('id'));
        $this->articleRepository->setDeleteArticle($article);

        return $this->redirectToRoute("admin_articlecategories", ['parentid' => $article->getParent()->getId()] );
    }

    /**
     * @Route("admin/articles/sortimages.json", name="admin_articles_sortimages")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function sortImages(Request $request)
    {
        $rawSortArray = $request->get('files');

        $sortArray = array();
        foreach($rawSortArray as $rawImageName) {
            $sortArray[] = htmlspecialchars($rawImageName);
        }

        $article = $this->articleRepository->getOne($request->get('id'));
        $article->setImageArray($sortArray);
        $this->articleRepository->setUpdateArticle($article);

        return $this->json([
            'result' => 'success',
        ]);
    }

    /**
     * @Route("admin/articles/deletephoto", name="admin_articles_deletephoto")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deletePhoto(Request $request)
    {
        $fileToDelete = $request->get('file');
        $articleId = $request->get('id');

        $article = $this->articleRepository->getOne($articleId);
        $this->articleRepository->deletePhoto($article, $fileToDelete);

        return $this->redirectToRoute("admin_articles_edit", ['id' => $articleId] );

    }



}