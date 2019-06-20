<?php
// src/Controller/WorkController.php

namespace App\Controller;

use App\Entity\OriginalWork;
use App\Entity\Quote;
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
        $worksQuery = $this->getDoctrine()
            ->getRepository(OriginalWork::class)
            ->createQueryBuilder('o')
            ->orderBy('o.title')
            ->getQuery();

        $works = $paginator->paginate(
            $worksQuery,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('Inside/Work/index.html.twig', array('works' => $works));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_work_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->find($id);

        $quotesQuery = $this->getDoctrine()
            ->getRepository(Quote::class)
            ->createQueryBuilder('q')
            ->andWhere('q.originalWork = :work')
            ->setParameter('work', $work)
            ->getQuery()
        ;


        $quotes = $paginator->paginate(
            $quotesQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Inside/Quote/index.html.twig', array('quotes' => $quotes));
    }

    /**
     * @Route("/dates", name="qtf_work_dates")
     */
    public function indexDates(Environment $twig, Request $request, PaginatorInterface $paginator)
    {


        return $this->render('Inside/Date/index.html.twig');
    }

  





}