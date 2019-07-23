<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/in/quote", requirements={
 *     "_locale"="%app.locales%"
 * }))
 */
class QuoteController extends AbstractController
{

    /**
     * @Route("/", name="qtf_quote_index")
     */
    public function index(Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        if ($user == null)
        {
            return $this->redirectToRoute('qtf_welcome_index');
        }

        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAll($user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $translated = $translator->trans("All quotes");
        $displayTitle = $translated;

        if ($this->getDoctrine()->getRepository(Quote::class)->getAll($user) == null) {
            $translated = $translator->trans("It seems you don't have any quote in your account yet. Add one by clicking the '+' button");
            $this->addFlash('warning', $translated);

        }

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/view/{id}", name="qtf_quote_view", requirements={"id" = "\d+"})
     */
    public function view($id, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $quote = $this->getDoctrine()->getRepository(Quote::class)->getQuoteById($id, $user);
        if ($quote == null) {
            $translated = $translator->trans('Oops! Something went wrong. The quote could not be found.');
            $this->addFlash('error', $translated);
            return $this->redirectToRoute('qtf_quote_index');
        } else {

            return $this->render('Inside/Quote/singleView.html.twig', array('quote' => $quote));
        }
    }

    /**
     * @Route("/create", name="qtf_quote_create")
     */
    public function create(Request $request, TranslatorInterface $translator)
    {
        $id = 0; //fake id for the cancel button
        $user = $this->getUser();
        $quote = new Quote();
        $quote->setUser($user);

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quote);
            $em->flush();

            $translated = $translator->trans('The quote has been added.');
            $this->addFlash('success', $translated);

            return $this->redirectToRoute('qtf_quote_view', array('id' => $quote->getId()));
        }


        return $this->render('Inside/Quote/form.html.twig', ['form' => $form->createView(), 'id' => $id]);
    }

    /**
     * @Route("/edit/{id}_{caller}", name="qtf_quote_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, $caller, Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $currentQuote = $this->getDoctrine()->getRepository(Quote::class)->getQuoteById($id, $user);

        if ($currentQuote == null) {
            $translated = $translator->trans('Oops! Something went wrong. The quote could not be found.');
            $this->addFlash('error', $translated);
            return $this->redirectToRoute('qtf_quote_index');
        } else {
            $form = $this->createForm(QuoteType::class, $currentQuote);

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($currentQuote);
                    $em->flush();

                    $translated = $translator->trans('The quote has been modified and saved.');
                    $this->addFlash('success', $translated);

                    return $this->redirectToRoute($caller, array('id' => $id));
                }
            }


            return $this->render('Inside/Quote/form.html.twig', array('form' => $form->createView(), 'previousPage' => $caller, 'id' => $id));
        }


    }

    /**
     * @Route("/{id}/delete", name="qtf_quote_delete", requirements={"id" = "\d+"})
     */
    public function delete($id, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $quote = $this->getDoctrine()->getRepository(Quote::class)->getQuoteById($id, $user);

        if ($quote == null) {
            $translated = $translator->trans('Oops! Something went wrong. The quote could not be found.');
            $this->addFlash('error', $translated);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($quote);
            $em->flush();
            $translated = $translator->trans('The quote has been deleted.');
            $this->addFlash('success', $translated);
        }


        return $this->redirectToRoute('qtf_quote_index');
    }

    /**
     * @Route("/view/random", name="qtf_quote_random")
     */
    public function viewRandom(TranslatorInterface $translator)
    {
        $user = $this->getUser();
        try {
            $quote = $this->getDoctrine()->getRepository(Quote::class)->findRandom($user);

            if ($quote == null) {
                return $this->redirectToRoute('qtf_quote_index');
            }

            return $this->render('Inside/Quote/singleView.html.twig', array('quote' => $quote));
        } catch (Exception $ex) {
            $translated = $translator->trans('Oops! Something went wrong : could not generate a random quote.');
            $this->addFlash('error', $translated);
            return $this->redirectToRoute('qtf_quote_index');
        }
    }


}