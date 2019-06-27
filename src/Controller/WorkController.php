<?php
// src/Controller/WorkController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\OriginalWork;
use App\Entity\Quote;
use App\Form\AuthorType;
use App\Form\OriginalWorkType;
use App\Repository\OriginalWorkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/work")
 */
class WorkController extends AbstractController
{

  /**
   * @Route("/", name="qtf_work_index")
   */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $worksQuery = $this->getDoctrine()->getRepository(OriginalWork::class)->createQueryFindAll();
        $works = $paginator->paginate($worksQuery, $request->query->getInt('page', 1),30);

        return $this->render('Inside/Work/index.html.twig', array('works' => $works));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_work_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->find($id);
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindByOriginalWork($work);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);
        $displayTitle = "All quotes for ". $work->getTitle();

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/dates", name="qtf_work_dates")
     */
    public function indexDates(Environment $twig)
    {
        $dates = $this->getDoctrine()->getRepository(OriginalWork::class)->findDates();

        return $this->render('Inside/Date/index.html.twig', array('dates' => $dates));
    }

    /**
     * @Route("/dates/{year}/quotes", name="qtf_work_year", requirements={"year" = "^-?[1-9]\d*$"})
     */
    public function viewQuotesByYear($year, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByYear($year);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);
        $year = $year == 9999 ? 'undefined date' : $year;
        $displayTitle = "All quotes for ".$year;

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create", name="qtf_work_create")
     */
    public function create(Request $request, PaginatorInterface $paginator)
    {
        $work = new OriginalWork();

        $form = $this->createForm(OriginalWorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();

            $this->addFlash('success', 'The original work has been added.');

            return $this->redirectToRoute('qtf_quote_create');
        }


        return $this->render('Inside/Work/form.html.twig', ['form' => $form->createView()]);
    }
  





}