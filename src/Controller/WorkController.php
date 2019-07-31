<?php

// src/Controller/WorkController.php

namespace App\Controller;

use App\Entity\OriginalWork;
use App\Entity\Quote;
use App\Form\OriginalWorkType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/{_locale}/in/work", requirements={
 *     "_locale"="%app.locales%"
 * }))
 * @IsGranted("ROLE_USER")
 */
class WorkController extends AbstractController
{
    /**
     * @Route("/", name="qtf_work_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $worksQuery = $this->getDoctrine()->getRepository(OriginalWork::class)->createQueryFindAll($user);
        $works = $paginator->paginate($worksQuery, $request->query->getInt('page', 1), 15);
        $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedWork($user);

        return $this->render('Inside/Work/index.html.twig', array('works' => $works, 'undefined' => $undefinedCount));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_work_quotes", requirements={"id" = "\d+"})
     * @param $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function listQuotes($id, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);

        if (null == $work) {
            $translated = $translator->trans('Oops! Something went wrong. The original work could not be found.');
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_work_index');
        } else {
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindByOriginalWork($work, $user);
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
            $translated = $translator->trans('All quotes for ');
            $displayTitle = $translated.$work->getTitle();

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
        }
    }

    /**
     * @Route("/undefined/quotes", name="qtf_work_quotes_undefined")
     */
    public function listUndefinedQuotes(Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedWork($user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $translated = $translator->trans('All quotes with undefined original work');
        $displayTitle = $translated;

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/dates", name="qtf_work_dates")
     * @return Response
     */
    public function indexDates()
    {
        $user = $this->getUser();
        $dates = $this->getDoctrine()->getRepository(OriginalWork::class)->findDates($user);

        return $this->render('Inside/Date/index.html.twig', array('dates' => $dates));
    }

    /**
     * @Route("/dates/{year}/quotes", name="qtf_work_year", requirements={"year" = "^-?[1-9]\d*$"})
     * @param $year
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function viewQuotesByYear($year, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByYear($year, $user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $translatedYear = $translator->trans('undefined date');
        $year = 9999 == $year ? $translatedYear : $year;
        $translated = $translator->trans('All quotes for ');
        $displayTitle = $translated.$year;

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create_{caller}", name="qtf_work_create")
     * @param $caller
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function create($caller, Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $work = new OriginalWork();
        $work->setUser($user);

        $form = $this->createForm(OriginalWorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();

            $translated = $translator->trans('The original work has been added.');
            $this->addFlash('success', $translated);

            return $this->redirectToRoute($caller);
        }

        return $this->render('Inside/Work/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit_{caller}", name="qtf_work_edit", requirements={"id" = "\d+"})
     * @param $id
     * @param $caller
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function edit($id, $caller, Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);

        if (null == $work) {
            $translated = $translator->trans('Oops! Something went wrong. The original work could not be found.');
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_work_index');
        } else {
            $form = $this->createForm(OriginalWorkType::class, $work);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($work);
                $em->flush();

                $translated = $translator->trans('The original work has been added.');
                $this->addFlash('success', $translated);

                return $this->redirectToRoute($caller);
            }

            return $this->render('Inside/Work/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_work_delete", requirements={"id" = "\d+"})
     * @param $id
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    public function delete($id, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $work = $this->getDoctrine()->getRepository(OriginalWork::class)->getWorkById($id, $user);

        if (null == $work) {
            $translated = $translator->trans('Oops! Something went wrong. The original work could not be found.');
            $this->addFlash('error', $translated);
        } elseif (count($work->getQuotes()) > 0) {
            $translated = $translator->trans('This original work can not be deleted : it references quotes in the database.');
            $this->addFlash('warning', $translated);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($work);
            $em->flush();
            $translated = $translator->trans('The original work has been deleted.');
            $this->addFlash('success', $translated);
        }

        return $this->redirectToRoute('qtf_work_index');
    }
}
