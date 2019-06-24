<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Form\QuoteType;

/**
 * @Route("/quote")
 */
class QuoteController extends AbstractController
{

    /**
     * @Route("/", name="qtf_quote_index")
     */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAll();
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1),10);
        $displayTitle = "All quotes";

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/view/{id}", name="qtf_quote_view", requirements={"id" = "\d+"})
     */
    public function view($id)
    {
        $quote = $this->getDoctrine()->getRepository(Quote::class)->find($id);

        return $this->render('Inside/Quote/singleView.html.twig', array('quote' => $quote));
    }

    /**
     * @Route("/create", name="qtf_quote_create")
     */
    public function create(Request $request)
    {

        $quote = new Quote();

        // *** A SUPPRIMER QUAND LA GESTION UTILISATEUR SERA IMPLEMENTEE ***
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        $quote->setUser($user);
        // ***


        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'The quote has been added.');

            return $this->redirectToRoute('qtf_quote_view', array('id' => $quote->getId()));
        }


        return $this->render('Inside/Quote/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="qtf_quote_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, Request $request)
    {
        $currentQuote = $this->getDoctrine()->getRepository(Quote::class)->find($id);
        $form = $this->createForm(QuoteType::class, $currentQuote);

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($currentQuote);
                $em->flush();

                $this->addFlash('success', 'The quote has been modified and saved.');

                return $this->redirectToRoute('qtf_quote_view', array('id' => $currentQuote->getId()));
            }
        }


        return $this->render('Inside/Quote/form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/delete/{id}", name="qtf_quote_delete", requirements={"id" = "\d+"})
     */
    public function delete($id)
    {
        return new Response("Suppression de l'annonce d'id : ".$id);
    }

    /**
     * @Route("/view/random", name="qtf_quote_random")
     */
    public function viewRandom()
    {
        try
        {
            $quote = $this->getDoctrine()->getRepository(Quote::class)->findRandom();
            return $this->render('Inside/Quote/singleView.html.twig', array('quote' => $quote));
        }
        catch (Exception $ex)
        {
            $this->addFlash('error', 'Oops! Something went wrong : could not generate a random quote.');
            return $this->redirectToRoute('qtf_quote_index');
        }
    }



}