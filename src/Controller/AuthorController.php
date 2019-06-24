<?php
// src/Controller/AuthorController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Quote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/author")
 */
class AuthorController extends AbstractController
{

  /**
   * @Route("/", name="qtf_author_index")
   */
  public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
  {
  	$authorsQuery = $this->getDoctrine()->getRepository(Author::class)->createQueryFindAll();
  	$authors = $paginator->paginate($authorsQuery, $request->query->getInt('page', 1),30);

  	return $this->render('Inside/Author/index.html.twig', array('authors' => $authors));
  }

  /**
   * @Route("/{id}/quotes", name="qtf_author_quotes", requirements={"id" = "\d+"})
   */
  public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
  {
  	$author = $this->getDoctrine()->getRepository(Author::class)->find($id);
  	$quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByAuthor($author);
  	$quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);
      $displayTitle = "All quotes for ".$author->getDisplayName();

      return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
  }

  





}