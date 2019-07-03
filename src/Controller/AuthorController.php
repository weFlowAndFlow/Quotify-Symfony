<?php
// src/Controller/AuthorController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Quote;
use App\Entity\User;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\AuthorType;

/**
 * @Route("/in/author")
 */
class AuthorController extends AbstractController
{

    /**
     * @Route("/", name="qtf_author_index")
     */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $authorsQuery = $this->getDoctrine()->getRepository(Author::class)->createQueryFindAll();
        $authors = $paginator->paginate($authorsQuery, $request->query->getInt('page', 1), 15);

        $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedAuthor();

        return $this->render('Inside/Author/index.html.twig', array('authors' => $authors, 'undefined' => $undefinedCount));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_author_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Request $request, PaginatorInterface $paginator)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if ($author == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The author could not be found.');
            return $this->redirectToRoute('qtf_author_index');
        } else {
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByAuthor($author);
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
            $displayTitle = "All quotes for " . $author->getDisplayName();

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
        }
    }

    /**
     * @Route("/undefined/quotes", name="qtf_author_quotes_undefined")
     */
    public function listUndefinedQuotes(Request $request, PaginatorInterface $paginator)
    {
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedAuthor();
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $displayTitle = "All anonymous quotes";

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create", name="qtf_author_create")
     */
    public function create(Request $request, PaginatorInterface $paginator)
    {
        $caller = $request->query->get('caller');

        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $this->addFlash('success', 'The author has been added.');

            return $this->redirectToRoute($caller);
        }


        return $this->render('Inside/Author/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit", name="qtf_author_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, Request $request, PaginatorInterface $paginator)
    {
        $caller = $request->query->get('caller');
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if ($author == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The author could not be found.');
            return $this->redirectToRoute('qtf_author_index');
        } else {
            $form = $this->createForm(AuthorType::class, $author);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($author);
                $em->flush();

                $this->addFlash('success', 'The author has been modified.');

                return $this->redirectToRoute($caller);
            }


            return $this->render('Inside/Author/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_author_delete", requirements={"id" = "\d+"})
     */
    public function delete($id, Request $request, PaginatorInterface $paginator)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if ($author == null) {
            $this->addFlash('error', 'Oops! Something went wrong. The author could not be found.');
        } elseif (count($author->getQuotes()) > 0) {
            $this->addFlash('warning', 'This author can not be deleted : it references quotes in the database.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();
            $this->addFlash('success', 'The author has been deleted.');
        }


        return $this->redirectToRoute('qtf_author_index');
    }


}