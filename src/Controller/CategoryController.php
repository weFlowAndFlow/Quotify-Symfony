<?php
// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{

  /**
   * @Route("/", name="qtf_category_index")
   */
  public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
  {
    $categoriesQuery = $this->getDoctrine()->getRepository(Category::class)->createQueryFindAll();
    $categories = $paginator->paginate($categoriesQuery, $request->query->getInt('page', 1),15);
    $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedCategory();

    return $this->render('Inside/Category/index.html.twig', array('categories' => $categories, 'undefined' => $undefinedCount));
  }


    /**
     * @Route("/{id}/quotes", name="qtf_category_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
            return $this->redirectToRoute('qtf_category_index');
        }
        else {
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByCategory($category);
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
            $displayTitle = "All quotes for " . $category->getName() . " category";

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
        }
    }


    /**
     * @Route("/undefined/quotes", name="qtf_category_quotes_undefined")
     */
    public function listUndefinedQuotes(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedCategory();
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
            $displayTitle = "All uncategorized quotes";

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create", name="qtf_category_create")
     */
    public function create(Request $request, PaginatorInterface $paginator)
    {
        $caller = $request->query->get('caller');
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The category has been added.');

            return $this->redirectToRoute($caller);
        }


        return $this->render('Inside/Category/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit", name="qtf_category_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, Request $request, PaginatorInterface $paginator)
    {
        $caller = $request->query->get('caller');
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);


        if ($category == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
            return $this->redirectToRoute('qtf_category_index');
        }
        else {

            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                $this->addFlash('success', 'The category has been modified.');

                return $this->redirectToRoute($caller);
            }


            return $this->render('Inside/Category/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_category_delete", requirements={"id" = "\d+"})
     */
    public function delete($id, Request $request, PaginatorInterface $paginator)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
        }
        elseif (count($category->getQuotes()) > 0)
        {
            $this->addFlash('warning', 'This category can not be deleted : it references quotes in the database.');
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'The category has been deleted.');
        }


        return $this->redirectToRoute('qtf_category_index');
    }
  





}