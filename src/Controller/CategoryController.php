<?php
// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    $categories = $paginator->paginate($categoriesQuery, $request->query->getInt('page', 1),30);

    return $this->render('Inside/Category/index.html.twig', array('categories' => $categories));
  }


  /**
   * @Route("/{id}/quotes", name="qtf_category_quotes", requirements={"id" = "\d+"})
   */
  public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
  {
    $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
    $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByCategory($category);
    $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);

    return $this->render('Inside/Quote/index.html.twig', array('quotes' => $quotes));
  }

  





}