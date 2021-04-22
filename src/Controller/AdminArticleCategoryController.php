<?php


namespace flexycms\FlexyArticlesBundle\Controller;

use flexycms\FlexyArticlesBundle\EntityRequest\ArticleCategoryRequest;
use flexycms\FlexyArticlesBundle\Form\ArticleCategoryType;
use flexycms\FlexyArticlesBundle\Repository\ArticleCategoryRepository;
use flexycms\FlexyArticlesBundle\Repository\ArticleRepository;
use flexycms\FlexyAdminFrameBundle\Controller\AdminBaseController;
use flexycms\BreadcrumbsBundle\Utils\Breadcrumbs;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleCategoryController extends AdminBaseController
{
    private $categoryRepository;
    private $articleRepository;

    public function __construct(ArticleCategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/admin/articlecategories", name="admin_articlecategories")
     */
    public function list(Request $request)
    {
        $forRender = parent::renderDefault();

        $parentId = $request->get('parentid');

        $breadcrumbs = new Breadcrumbs();
        $articles = [];

        $forRender['upId'] = false;
        if (!$parentId)
        {
            $forRender['title'] = 'Категориии статей';
            $forRender['categories'] = $this->categoryRepository->findBy(['parent' => null]);

            $breadcrumbs->append($this->generateUrl("admin_home"), 'Главная');
            $breadcrumbs->append($this->generateUrl("admin_articlecategories"), 'Категории статей');

            $forRender['parentId'] = false;
        }
        else
        {
            $parent = $this->categoryRepository->getOne($parentId);
            $current = clone($parent);
            while ($current) {
                $breadcrumbs->prepend($this->generateUrl("admin_articlecategories", ['parentid' => $current->getId()]), $current->getName());
                $current = $current->getParent();
            }

            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories"), 'Категории статей');
            $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');

            $forRender['parentId'] = $parentId;

            $forRender['title'] = 'Категориии статей';
            $forRender['categories'] = $this->categoryRepository->findBy(['parent' => $parentId]);

            $parent2 = $parent->getParent();

            $articles = $this->articleRepository->findBy(['parent' => $parent]);

            if ($parent2) $forRender['upId'] = $parent2->getId();
            else $forRender['upId'] = null;
        }

        $forRender['articles'] = $articles;

        $forRender['ajax'] = $this->generateUrl("admin_articlescategory_json", ['parentid' => $parentId]);

        $forRender['breadcrumbs'] = $breadcrumbs;

        return $this->render("@FlexyArticles/articlelist.html.twig", $forRender);
    }


    /**
     * @Route("/admin/articles.json", name="admin_articlescategory_json")
     */
    public function listJSON(Request $request)
    {
        $draw = $request->get("draw");

        $parentId = $request->get('parentid');
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get("search");
        $order = $request->get("order");

        if ($length == -1) $length = 10;

        $searchValue = '';
        if (isset($search['value'])) {
            $searchValue = $search['value'];
        }

        // Определяем по какому полю сортировать
        $orderColumn = null;
        $orderDirection  = 'ASC';
        if (isset($order[0]) && isset($order[0]['column'])) $orderColumn = $order[0]['column'];
        if (isset($order[0]) && isset($order[0]['dir']) && $order[0]['dir'] == 'desc') $orderDirection = 'DESC';

        // Находим, сколько всего у нас категорий
        $allCategoryCount = $this->categoryRepository->countAll($parentId);

        // Находим, сколько категорий попадают под фильтр без ограничения по страницам
        $categoryCount = $this->categoryRepository->countBySearch($parentId, $searchValue);

        // Находим все категории
        $orderColumnName = null;
        if ($orderColumn == 1) $orderColumnName = 'name';
        if ($orderColumn == 2) $orderColumnName = 'defaultArticleType';
        if ($orderColumn == 3) $orderColumnName = 'createAt';
        if ($orderColumn == 4) $orderColumnName = 'code';
        if ($orderColumn == 5) $orderColumnName = 'sort';

        $categories = $this->categoryRepository->getBySearch(
            $parentId,
            $searchValue,
            ($orderColumnName === null)?null:([$orderColumnName, $orderDirection]),
            [$start, $length]
        );


        $articles = array();
        // Находим, сколько всего у нас статей
        $allArticleCount = $this->articleRepository->countAllByCategory($parentId);

        // Находим, сколько статей попадают под фильтр без ограничения по страницам
        $articleCount = $this->articleRepository->countSearchByCategory($parentId, $searchValue);

        // Определяем start и length, исходя из полученных категорий
        if ($start + $length > count($categories))
        {
            $aStart = $start - $categoryCount;
            $aLength =  $length - count($categories);

            if ($aStart < 0) $aStart = 0;
            if ($aLength < 0) $aLength = 0;

            if ($aLength) {
                // Находим все статьи
                $orderColumnName = null;
                if ($orderColumn == 2) $orderColumnName = 'title';
                if ($orderColumn == 3) $orderColumnName = 'articleType';
                if ($orderColumn == 4) $orderColumnName = 'createAt';
                if ($orderColumn == 5) $orderColumnName = 'code';
                if ($orderColumn == 6) $orderColumnName = 'sort';
                $articles = $this->articleRepository->getSearchByCategory(
                    $parentId,
                    $searchValue,
                    ($orderColumnName === null) ? null : ([$orderColumnName, $orderDirection]),
                    [$aStart, $aLength]
                );
            }
        }





        // Соединяем объекты в массив для передачи в таблицу
        $data = array();
        foreach($categories as $category)
        {
            $item = array();
            $item[] = '<span class="datatable-row-id" data-id="c_' . $category->getId() . '"></span>';

            $item[] = '<a href="' . $this->generateUrl("admin_articlecategories_edit", ["id" => $category->getId()]) . '" class="btn btn-sm btn-primary"><i class="far fa-edit fa-fw"></i></a>&nbsp;<a href="' . $this->generateUrl("admin_articlecategories_delete", ['id' => $category->getId()]) . '" class="btn btn-sm btn-danger" data-title="Подтвердите действие" data-message="Удалить категорию и содержащиеся в ней статьи?"><i class="far fa-trash-alt fa-fw"></i></a>';

            $item[] = '<a href="' . $this->generateUrl("admin_articlecategories", ["parentid" => $category->getId()]) . '"><i class="fas fa-folder fa-fw"></i>&nbsp;'.$category->getName().'</a>';

            $item[] = "категория";
            $item[] = $category->getUpdateAt()->format("d.m.Y H:i:s");
            $item[] = $category->getCode();
            $item[] = '0';
            $data[] = $item;
        }

        foreach($articles as $article)
        {
            $name = '<i class="far fa-file-alt"></i>&nbsp;'.$article->getTitle();
            if (!$article->getIsPublished()) $name = "<i class='unpublished'>{$name}</i>";

            $item = array();
            $item[] = '<span class="datatable-row-id" data-id="a_' . $article->getId() . '"></span>';

            $item[] = '<a href="' . $this->generateUrl("admin_articles_edit", ["id" => $article->getId()]) . '" class="btn btn-sm btn-primary"><i class="far fa-edit fa-fw"></i></a>&nbsp;<a href="' . $this->generateUrl("admin_articles_delete", ['id' => $article->getId()]) . '" class="btn btn-sm btn-danger" data-title="Подтвердите действие" data-message="Удалить статью?"><i class="far fa-trash-alt fa-fw"></i></a>';
            $item[] = $name;
            $item[] = $article->getArticleType()->getName();
            $item[] = $article->getUpdateAt()->format("d.m.Y H:i:s");
            $item[] = $article->getCode();
            $item[] = $article->getSort();
            $data[] = $item;
        }

        $recordsTotal = 100;
        $recordsFiltered = 50;

        return $this->json([
            "data" => $data,
            'draw' => $draw,
            'recordsTotal' => $allCategoryCount + $allArticleCount,
            'recordsFiltered' => $categoryCount + $articleCount,

        ]);
    }


    /**
     * @Route("admin/articlecategories/create", name="admin_articlecategories_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request, ArticleCategoryRequest $categoryRequest)
    {
        $parentId = $request->get('parentid');
        $parent = null;
        $breadcrumbs = new Breadcrumbs();

        if (!$parentId) {
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories_create"), 'Добавить категорию');
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories"), 'Категории статей');
            $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        }
        else {
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories_create"), 'Добавить категорию');
            $parent = $this->categoryRepository->getOne($parentId);
            $current = clone($parent);
            while ($current) {
                $breadcrumbs->prepend($this->generateUrl("admin_articlecategories", ['parentid' => $current->getId()]), $current->getName());
                $current = $current->getParent();
            }
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories"), 'Категории статей');
            $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        }
        try {

            if ($parentId)
            {
                $parent = $this->categoryRepository->getOne($parentId);
                $categoryRequest->createForParent($parent);
            }
            else
            {
                $categoryRequest->create();
            }

        }
        catch (\Exception $ex)
        {
            $this->addFlash("error", $ex->getMessage());
            return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parentId] );
        }




        $form = $this->createForm(ArticleCategoryType::class, $categoryRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $categoryRequest->uploadImage($uploadedFile);
            }
            $categoryRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articlecategories_edit", ['id' => $categoryRequest->get()->getId()] );
            }

            if (!$parent) {
                return $this->redirectToRoute("admin_articlecategories");
            } else {
                return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parent->getId()]);
            }
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Добавление категории статьи";
        $forRender['form'] = $form->createView();
        $forRender['breadcrumbs'] = $breadcrumbs;

        $forRender['parentId'] = $parentId;

        return $this->render("@FlexyArticles/categoryform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articlecategories/edit", name="admin_articlecategories_edit")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, ArticleCategoryRequest $categoryRequest)
    {
        $categoryId = $request->get('id');
        $category = $this->categoryRepository->getOne($categoryId);
        $parent = $category->getParent();

        $breadcrumbs = new Breadcrumbs();

        if (!$parent) {

            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories_edit", ['id' => $categoryId]), 'Редактировать категорию');
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories"), 'Категории статей');
            $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        }
        else {
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories_edit", ['id' => $categoryId]), 'Редактировать категорию');
            $current = clone($parent);
            while ($current) {
                $breadcrumbs->prepend($this->generateUrl("admin_articlecategories", ['parentid' => $current->getId()]), $current->getName());
                $current = $current->getParent();
            }
            $breadcrumbs->prepend($this->generateUrl("admin_articlecategories"), 'Категории статей');
            $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        }





        $categoryRequest->load($categoryId);

        $form = $this->createForm(ArticleCategoryType::class, $categoryRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $categoryRequest->uploadImage($uploadedFile);
            }

            $categoryRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articlecategories_edit", ['id' => $categoryRequest->get()->getId()] );
            }

            if (!$parent) {
                return $this->redirectToRoute("admin_articlecategories");
            } else {
                return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parent->getId()]);
            }
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Редактирование категории статей";
        $forRender['form'] = $form->createView();
        $forRender['breadcrumbs'] = $breadcrumbs;

        if (!$parent) {
            $forRender['parentId'] = null;
        } else {
            $forRender['parentId'] = $parent->getId();
        }

        return $this->render("@FlexyArticles/categoryform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articlecategories/delete", name="admin_articlecategories_delete")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function delete(Request $request)
    {
        $category = $this->categoryRepository->getOne($request->get('id'));
        $this->categoryRepository->delete($category);

        $parent = $category->getParent();
        if ($parent) return $this->redirectToRoute("admin_articlecategories", ['parentid' => $parent->getId()]);
        return $this->redirectToRoute("admin_articlecategories");
    }


    /**
     * @Route("admin/articlecategories/deletelist", name="admin_articlecategories_deletelist")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deletelist(Request $request)
    {
        $ids = $request->get('data');

        if (is_array($ids)) {
            foreach($ids as $idc) {

                //Определяем категория это или статья
                if (strpos($idc, '_') === false) continue;

                list($type, $id) = explode('_', $idc);

                if ($type == 'a') {
                    $article = $this->articleRepository->getOne($id);
                    if ($article) $this->articleRepository->setDeleteArticle($article);
                }
                if ($type == 'c') {
                    $category = $this->categoryRepository->getOne($id);
                    if ($category) $this->categoryRepository->delete($category);
                }
            }
        }

        return $this->json([
            "action" => 'reload',
        ]);
    }

}