<?php

// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\OriginalWork;
use App\Entity\Quote;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/{_locale}/in/search", requirements={
 *     "_locale"="%app.locales%"
 * }))
 * @IsGranted("ROLE_USER")
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

        if (null == $keywords) {
            $keywords = $request->get('keywords');
        }

        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQuerySearch($keywords, $user);
        $categoriesQuery = $this->getDoctrine()->getRepository(Category::class)->createQuerySearch($keywords, $user);
        $authorsQuery = $this->getDoctrine()->getRepository(Author::class)->createQuerySearch($keywords, $user);
        $originalWorksQuery = $this->getDoctrine()->getRepository(OriginalWork::class)->createQuerySearch($keywords, $user);

        $counts['quotes'] = $this->getDoctrine()->getRepository(Quote::class)->searchResultsSize($keywords, $user);
        $counts['categories'] = $this->getDoctrine()->getRepository(Category::class)->searchResultsSize($keywords, $user);
        $counts['authors'] = $this->getDoctrine()->getRepository(Author::class)->searchResultsSize($keywords, $user);
        $counts['works'] = $this->getDoctrine()->getRepository(OriginalWork::class)->searchResultsSize($keywords, $user);

        $results['quotes'] = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $results['categories'] = $paginator->paginate($categoriesQuery, $request->query->getInt('page', 1), 10);
        $results['authors'] = $paginator->paginate($authorsQuery, $request->query->getInt('page', 1), 10);
        $results['works'] = $paginator->paginate($originalWorksQuery, $request->query->getInt('page', 1), 10);

        $tab = $request->get('requestedTab');
        if (null == $tab) {
            if ($counts['quotes'] > 0) {
                $tab = 'quoteTab';
            } elseif ($counts['categories'] > 0) {
                $tab = 'categoryTab';
            } elseif ($counts['authors'] > 0) {
                $tab = 'authorTab';
            } elseif ($counts['works'] > 0) {
                $tab = 'workTab';
            } else {
                $tab = 'void';
            }
        }

        return $this->render('Inside/Search/index.html.twig', [
            'keywords' => $keywords,
            'results' => $results,
            'tab' => $tab,
            'counts' => $counts,
            'undefined' => 0, // used to prevent from displaying the undefined card that's shown in the menu list view
        ]);
    }

    /**
     * @Route("/{keywords}/quotes", name="qtf_search_quotes")
     */
    public function viewQuotes($keywords, Request $request, PaginatorInterface $paginator)
    {
    }

    /**
     * @Route("/{keywords}/categories", name="qtf_search_categories")
     */
    public function viewCategories(Request $request, PaginatorInterface $paginator)
    {
    }

    /**
     * @Route("/{keywords}/authors", name="qtf_search_authors")
     */
    public function viewAuthors(Request $request, PaginatorInterface $paginator)
    {
    }

    /**
     * @Route("/{keywords}/works", name="qtf_search_works")
     */
    public function viewWorks(Request $request, PaginatorInterface $paginator)
    {
    }
}
