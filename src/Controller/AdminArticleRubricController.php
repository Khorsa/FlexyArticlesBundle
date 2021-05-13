<?php


namespace flexycms\FlexyArticlesBundle\Controller;

use flexycms\FlexyAdminFrameBundle\Controller\AdminBaseController;
use flexycms\FlexyArticlesBundle\EntityRequest\ArticleRubricRequest;
use flexycms\FlexyArticlesBundle\Form\ArticleRubricType;
use flexycms\FlexyArticlesBundle\Repository\ArticleRepository;
use flexycms\FlexyArticlesBundle\Repository\ArticleRubricRepository;
use flexycms\BreadcrumbsBundle\Utils\Breadcrumbs;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleRubricController extends AdminBaseController
{
    private $rubricRepository;
    private $articleRepository;

    public function __construct(ArticleRubricRepository $rubricRepository, ArticleRepository $articleRepository)
    {
        $this->rubricRepository = $rubricRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/admin/articlerubrics", name="admin_articlerubrics")
     */
    public function list(Request $request)
    {
        $parentId = $request->get('parentid');

        $forRender = parent::renderDefault();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->append($this->generateUrl("admin_home"), 'Главная');
        $breadcrumbs->append($this->generateUrl("admin_articlerubrics"), 'Рубрики статей');
        $forRender['breadcrumbs'] = $breadcrumbs;

        $forRender['upId'] = false;
        $forRender['title'] = 'Рубрики статей';

        $articles = [];
        $forRender['articles'] = $articles;
        $forRender['parentId'] = $parentId;

        $forRender['ajax'] = $this->generateUrl("admin_articlesrubric_json", ['parentid' => $parentId]);

        return $this->render("@FlexyArticles/rubriclist.html.twig", $forRender);
    }



    /**
     * @Route("/admin/articlesrubric.json", name="admin_articlesrubric_json")
     */
    public function listJSON(Request $request)
    {
        // TODO - нарушение DRY и вообще надо проверить!!

        $draw = $request->get("draw");

        $parentId = $request->get('parentid');
        $start = $request->get("start");
        $length = $request->get("length");

        if ($length == -1) $length = 10;

        $search = $request->get("search");
        $order = $request->get("order");

        $searchValue = '';
        if (isset($search['value'])) {
            $searchValue = $search['value'];
        }

        if ($parentId === null)
        {
            // Определяем по какому полю сортировать
            $orderColumn = null;
            $orderDirection  = 'ASC';
            if (isset($order[0]) && isset($order[0]['column'])) $orderColumn = $order[0]['column'];
            if (isset($order[0]) && isset($order[0]['dir']) && $order[0]['dir'] == 'desc') $orderDirection = 'DESC';

            // Находим, сколько всего у нас рубрик
            $allRubricCount = $this->rubricRepository->countAll();

            // Находим, сколько рубрик попадают под фильтр без ограничения по страницам
            $rubricCount = $this->rubricRepository->countBySearch($searchValue);

            // Находим все рубрики
            $orderColumnName = null;
            if ($orderColumn == 2) $orderColumnName = 'name';
            if ($orderColumn == 3) $orderColumnName = 'section';
            if ($orderColumn == 4) $orderColumnName = 'createAt';
            if ($orderColumn == 5) $orderColumnName = 'code';

            $rubrics = $this->rubricRepository->getBySearch(
                $searchValue,
                ($orderColumnName === null) ? null : ([$orderColumnName, $orderDirection]),
                [$start, $length]
            );

            // Собираем рубрики в массив для передачи в таблицу
            $data = array();
            foreach($rubrics as $rubric)
            {
                $item = array();
                $item[] = '<span class="datatable-row-id" data-id="' . $rubric->getId() . '"></span>';
                $item[] = '<a href="' . $this->generateUrl("admin_articlerubrics_edit", ["id" => $rubric->getId()]) . '" class="btn btn-sm btn-primary"><i class="far fa-edit fa-fw"></i></a>&nbsp;<a href="' . $this->generateUrl("admin_articlerubrics_delete", ['id' => $rubric->getId()]) . '" class="btn btn-sm btn-danger" data-title="Подтвердите действие" data-message="Удалить рубрику? (содержащиеся в ней статьи удалены не будут)"><i class="far fa-trash-alt fa-fw"></i></a>';
                $item[] = '<a href="' . $this->generateUrl("admin_articlerubrics", ["parentid" => $rubric->getId()]) . '"><i class="fas fa-folder fa-fw"></i>&nbsp;'.$rubric->getName().'</a>';
                $item[] = $rubric->getSection();
                $item[] = $rubric->getUpdateAt()->format("d.m.Y H:i:s");
                $item[] = $rubric->getCode();
                $data[] = $item;
            }

            return $this->json([
                "data" => $data,
                'draw' => $draw,
                'recordsTotal' => $allRubricCount,
                'recordsFiltered' => $rubricCount,
            ]);
        }

        // Определён $parentId - рубрика, в которой мы сейчас находимся. Это - список статей

        // Определяем по какому полю сортировать
        $orderColumn = null;
        $orderDirection  = 'ASC';
        if (isset($order[0]) && isset($order[0]['column'])) $orderColumn = $order[0]['column'];
        if (isset($order[0]) && isset($order[0]['dir']) && $order[0]['dir'] == 'desc') $orderDirection = 'DESC';

        // Находим, сколько всего у нас статей в рубрике
        $allArticleCount = $this->articleRepository->countSearchByRubric($parentId, '');

        // Находим, сколько статей попадают под фильтр без ограничения по страницам
        $articleCount = $this->articleRepository->countSearchByRubric($parentId, $searchValue);

        // Находим все статьи
        $orderColumnName = null;
        if ($orderColumn == 2) $orderColumnName = 'title';
        if ($orderColumn == 3) $orderColumnName = 'articleType';
        if ($orderColumn == 4) $orderColumnName = 'createAt';
        if ($orderColumn == 5) $orderColumnName = 'code';

        $articles = $this->articleRepository->getSearchByRubric(
            $parentId,
            $searchValue,
            ($orderColumnName === null) ? null : ([$orderColumnName, $orderDirection]),
            [$start, $length]
        );

        // Собираем статьи в массив для передачи в таблицу
        $data = array();
        foreach($articles as $article)
        {
            $name = '<i class="far fa-file-alt"></i>&nbsp;'.$article->getTitle();
            if (!$article->getIsPublished()) $name = "<i class='unpublished'>{$name}</i>";

            $item = array();
            $item[] = '<span class="datatable-row-id" data-id="' . $article->getId() . '"></span>';
            $item[] = '<a href="' . $this->generateUrl(
                "admin_articles_edit",
                [
                    "id" => $article->getId(),
                    'backpath' => $this->generateUrl('admin_articlerubrics', ['parentid' => $parentId])
                ]) . '" class="btn btn-sm btn-primary"><i class="far fa-edit fa-fw"></i></a>&nbsp;<a href="' . $this->generateUrl("admin_articlerubrics_unlink", ['articleid' => $article->getId(), 'rubricid' => $parentId]) . '" class="btn btn-sm btn-danger" data-title="Подтвердите действие" data-message="Убрать статью из рубрики? (сама статья удалена не будет)"><i class="far fa-minus-square fa-fw"></i></a>';
            $item[] = $name;
            $item[] = $article->getArticleType()->getName();
            $item[] = $article->getUpdateAt()->format("d.m.Y H:i:s");
            $item[] = $article->getCode();
            $data[] = $item;
        }

        return $this->json([
            "data" => $data,
            'draw' => $draw,
            'recordsTotal' => $allArticleCount,
            'recordsFiltered' => $articleCount,
        ]);
    }






    /**
     * @Route("admin/articlerubrics/unlink", name="admin_articlerubrics_unlink")
     * @param Request $request
     */
    public function unlinkArticle(Request $request)
    {
        $rubricId = $request->get('rubricid');
        $articleId = $request->get('articleid');

        $article = $this->articleRepository->getOne($articleId);
        $rubric = $this->rubricRepository->getOne($rubricId);

        $article->removeRubric($rubric);

        $this->articleRepository->setUpdateArticle($article);

        return $this->redirectToRoute("admin_articlerubrics", ['parentid' => $rubricId]);
    }



    /**
     * @Route("admin/articlerubrics/create", name="admin_articlerubrics_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request, ArticleRubricRequest $rubricRequest)
    {
        $breadcrumbs = new Breadcrumbs();

        $breadcrumbs->prepend($this->generateUrl("admin_articlerubrics_create"), 'Добавить рубрику');
        $breadcrumbs->prepend($this->generateUrl("admin_articlerubrics"), 'Рубрики статей');
        $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');

        try {
            $rubricRequest->create();
        }
        catch (\Exception $ex)
        {
            $this->addFlash("danger", $ex->getMessage());
            return $this->redirectToRoute("admin_articlerubrics");
        }

        $form = $this->createForm(ArticleRubricType::class, $rubricRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $rubricRequest->uploadImage($uploadedFile);
            }
            $rubricRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articlerubrics_edit", ['id' => $rubricRequest->get()->getId()] );
            }
            return $this->redirectToRoute("admin_articlerubrics");
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Добавление рубрики статей";
        $forRender['form'] = $form->createView();
        $forRender['breadcrumbs'] = $breadcrumbs;
        $forRender['rubricSectionList'] = $this->rubricRepository->getRubricSectionList();

        return $this->render("@FlexyArticles/rubricform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articlerubrics/edit", name="admin_articlerubrics_edit")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, ArticleRubricRequest $rubricRequest)
    {
        $rubricId = $request->get('id');
        $rubric = $this->rubricRepository->getOne($rubricId);

        $breadcrumbs = new Breadcrumbs();

        $breadcrumbs->prepend($this->generateUrl("admin_articlerubrics_edit", ['id' => $rubricId]), 'Редактировать рубрику');
        $breadcrumbs->prepend($this->generateUrl("admin_articlerubrics"), 'Рубрики статей');
        $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');

        $rubricRequest->load($rubricId);

        $form = $this->createForm(ArticleRubricType::class, $rubricRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $rubricRequest->uploadImage($uploadedFile);
            }

            $rubricRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articlerubrics_edit", ['id' => $rubricRequest->get()->getId()] );
            }

            return $this->redirectToRoute("admin_articlerubrics");
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Редактирование рубрики статей";
        $forRender['form'] = $form->createView();
        $forRender['breadcrumbs'] = $breadcrumbs;
        $forRender['rubricSectionList'] = $this->rubricRepository->getRubricSectionList();

        return $this->render("@FlexyArticles/rubricform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articlerubrics/delete", name="admin_articlerubrics_delete")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function delete(Request $request)
    {
        $rubric = $this->rubricRepository->getOne($request->get('id'));
        $this->rubricRepository->delete($rubric);
        return $this->redirectToRoute("admin_articlerubrics");
    }



    /**
     * @Route("admin/articlerubrics/deletelist", name="admin_articlerubrics_deletelist")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deletelist(Request $request)
    {
        $ids = $request->get('data');

        if (is_array($ids)) {
            foreach($ids as $id) {
                $rubric = $this->rubricRepository->getOne($id);
                if ($rubric) $this->rubricRepository->delete($rubric);
            }
        }

        return $this->json([
            "action" => 'reload',
        ]);
    }


    /**
     * @Route("admin/articlerubrics/unlinklist", name="admin_articlerubrics_unlinklist")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function unlinklist(Request $request)
    {
        $ids = $request->get('data');
        $rubricId = $request->get('rubricId');

        $rubric = $this->rubricRepository->getOne($rubricId);



        if (is_array($ids) && $rubricId) {
            foreach($ids as $id) {
                $article = $this->articleRepository->getOne($id);
                $article->removeRubric($rubric);
                $this->articleRepository->setUpdateArticle($article);
            }
        }

        return $this->json([
            "action" => 'reload',
        ]);
    }



}