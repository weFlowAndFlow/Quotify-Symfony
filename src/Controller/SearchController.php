<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\OriginalWork;
use App\Entity\Quote;
use App\Form\QuoteType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/in/search")
 */
class SearchController extends AbstractController
{

    /**
     * @Route("/", name="qtf_search_index")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $keywords = $request->get('searchInput');
        $results = [];

        $quotes = $this->getDoctrine()->getRepository(Quote::class)->search($keywords, $user);
        $categories = $this->getDoctrine()->getRepository(Category::class)->search($keywords, $user);
        $authors = $this->getDoctrine()->getRepository(Author::class)->search($keywords, $user);
        $originalWorks = $this->getDoctrine()->getRepository(OriginalWork::class)->search($keywords, $user);

        $results['quotes'] = $quotes;
        $results['categories'] = $categories;
        $results['authors'] = $authors;
        $results['works'] = $originalWorks;

        return $this->render('Inside/Search/index.html.twig', ['keywords' => $keywords, 'results' => $results]);
    }




}
