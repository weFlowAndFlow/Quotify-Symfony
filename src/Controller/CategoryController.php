<?php
// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Quote;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/in/category")
 */
class CategoryController extends AbstractController
{

    /**
     * @Route("/", name="qtf_category_index")
     */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {

        $user = $this->getUser();
        $categoriesQuery = $this->getDoctrine()->getRepository(Category::class)->createQueryFindAll($user);
        $categories = $paginator->paginate($categoriesQuery, $request->query->getInt('page', 1), 15);
        $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedCategory($user);

        return $this->render('Inside/Category/index.html.twig', array('categories' => $categories, 'undefined' => $undefinedCount));
    }


    /**
     * @Route("/{id}/quotes", name="qtf_category_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $category = $this->getDoctrine()->getRepository(Category::class)->getCategoryById($id, $user);

        if ($category == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
            return $this->redirectToRoute('qtf_category_index');
        } else {

            $user = $this->getUser();
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByCategory($category, $user);
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
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedCategory($user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $displayTitle = "All uncategorized quotes";

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create_{caller}", name="qtf_category_create")
     */
    public function create($caller, Request $request, PaginatorInterface $paginator)
    {
        $category = new Category();
        $user = $this->getUser();
        $category->setUser($user);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The category has been added.');

            return $this->redirectToRoute($caller);
        }


        return $this->render('Inside/Category/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit_{caller}", name="qtf_category_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, $caller, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $category = $this->getDoctrine()->getRepository(Category::class)->getCategoryById($id, $user);

        if ($category == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
            return $this->redirectToRoute('qtf_category_index');
        } else {

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
        $user = $this->getUser();
        $category = $this->getDoctrine()->getRepository(Category::class)->getCategoryById($id, $user);

        if ($category == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The category could not be found.');
        } elseif (count($category->getQuotes()) > 0) {
            $this->addFlash('warning', 'This category can not be deleted : it references quotes in the database.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'The category has been deleted.');
        }


        return $this->redirectToRoute('qtf_category_index');
    }


}