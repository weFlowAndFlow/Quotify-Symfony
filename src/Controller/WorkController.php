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
 * @Route("/in/work")
 */
class WorkController extends AbstractController
{

  /**
   * @Route("/", name="qtf_work_index")
   */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $worksQuery = $this->getDoctrine()->getRepository(OriginalWork::class)->createQueryFindAll($user);
        $works = $paginator->paginate($worksQuery, $request->query->getInt('page', 1),15);
        $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedWork($user);

        return $this->render('Inside/Work/index.html.twig', array('works' => $works, 'undefined' => $undefinedCount));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_work_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);


        if ($work == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The original work could not be found.');
            return $this->redirectToRoute('qtf_work_index');
        }
        else {
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindByOriginalWork($work, $user);
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
            $displayTitle = "All quotes for " . $work->getTitle();

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
        }
    }

    /**
     * @Route("/undefined/quotes", name="qtf_work_quotes_undefined")
     */
    public function listUndefinedQuotes(Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedWork($user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $displayTitle = "All quotes with undefined original work";

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/dates", name="qtf_work_dates")
     */
    public function indexDates(Environment $twig)
    {
        $user = $this->getUser();
        $dates = $this->getDoctrine()->getRepository(OriginalWork::class)->findDates($user);

        return $this->render('Inside/Date/index.html.twig', array('dates' => $dates));
    }

    /**
     * @Route("/dates/{year}/quotes", name="qtf_work_year", requirements={"year" = "^-?[1-9]\d*$"})
     */
    public function viewQuotesByYear($year, Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByYear($year, $user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);
        $year = $year == 9999 ? 'undefined date' : $year;
        $displayTitle = "All quotes for ".$year;

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create_{caller}", name="qtf_work_create")
     */
    public function create($caller, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $work = new OriginalWork();
        $work->setUser($user);

        $form = $this->createForm(OriginalWorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();

            $this->addFlash('success', 'The original work has been added.');

            return $this->redirectToRoute($caller);
        }


        return $this->render('Inside/Work/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit_{caller}", name="qtf_work_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, $caller, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);

        if ($work == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The original work could not be found.');
            return $this->redirectToRoute('qtf_work_index');
        }
        else {

            $form = $this->createForm(OriginalWorkType::class, $work);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($work);
                $em->flush();

                $this->addFlash('success', 'The original work has been added.');

                return $this->redirectToRoute($caller);
            }


            return $this->render('Inside/Work/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_work_delete", requirements={"id" = "\d+"})
     */
    public function delete($id, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);

        if ($work == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong. The original work could not be found.');
        }
        elseif (count($work->getQuotes()) > 0)
        {
            $this->addFlash('warning', 'This original work can not be deleted : it references quotes in the database.');
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($work);
            $em->flush();
            $this->addFlash('success', 'The original work has been deleted.');
        }


        return $this->redirectToRoute('qtf_work_index');
    }
  





}