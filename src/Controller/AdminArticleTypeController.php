<?php


namespace flexycms\FlexyArticlesBundle\Controller;

use flexycms\FlexyAdminFrameBundle\Controller\AdminBaseController;
use flexycms\FlexyArticlesBundle\EntityRequest\ArticleTypeRequest;
use flexycms\FlexyArticlesBundle\Form\ArticleTypeType;
use flexycms\FlexyArticlesBundle\Repository\ArticleTypeRepository;
use flexycms\BreadcrumbsBundle\Utils\Breadcrumbs;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleTypeController extends AdminBaseController
{
    private $typeRepository;

    public function __construct(ArticleTypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * @Route("/admin/articletypes", name="admin_articletypes")
     */
    public function index()
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Типы статей';
        $forRender['types'] = $this->typeRepository->getAll();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend("/admin/articletypes", 'Типы статей');
        $breadcrumbs->prepend("/admin", 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;

        return $this->render('@FlexyArticles/typelist.html.twig', $forRender);
    }

    /**
     * @Route("admin/articletypes/create", name="admin_articletypes_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request, ArticleTypeRequest $typeRequest)
    {
        $typeRequest->create();

        $form = $this->createForm(ArticleTypeType::class, $typeRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articletypes_edit", ['id' => $typeRequest->get()->getId()] );
            }


            return $this->redirectToRoute("admin_articletypes");
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Добавление типа статьи";
        $forRender['form'] = $form->createView();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend("/admin/articletypes/create", 'Добавление типа статей');
        $breadcrumbs->prepend("/admin/articletypes", 'Типы статей');
        $breadcrumbs->prepend("/admin", 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;

        return $this->render("@FlexyArticles/typeform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articletypes/edit", name="admin_articletypes_edit")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, ArticleTypeRequest $typeRequest)
    {
        $typeId = $request->get('id');
        $typeRequest->load($typeId);

        $form = $this->createForm(ArticleTypeType::class, $typeRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $typeRequest->save();

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute("admin_articletypes_edit", ['id' => $typeRequest->get()->getId()] );
            }


            return $this->redirectToRoute("admin_articletypes");
        }

        $forRender = parent::renderDefault();
        $forRender['title'] = "Редактирование типа статей";
        $forRender['form'] = $form->createView();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend("/admin/articletypes/edit?id={$typeId}", 'Редактирование типа статей');
        $breadcrumbs->prepend("/admin/articletypes", 'Типы статей');
        $breadcrumbs->prepend("/admin", 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;


        return $this->render("@FlexyArticles/typeform.html.twig", $forRender);
    }

    /**
     * @Route("admin/articletypes/delete", name="admin_articletypes_delete")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function delete(Request $request)
    {
        $category = $this->typeRepository->getOne($request->get('id'));
        $this->typeRepository->delete($category);

        return $this->redirectToRoute("admin_articletypes");
    }



}