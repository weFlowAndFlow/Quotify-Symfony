<?php

// src/Controller/AuthorController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Quote;
use App\Form\AuthorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @Route("/{_locale}/in/author", requirements={
 *     "_locale"="%app.locales%"
 * }))
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/", name="qtf_author_index")
     */
    public function index(Environment $twig, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $authorsQuery = $this->getDoctrine()->getRepository(Author::class)->createQueryFindAll($user);
        $authors = $paginator->paginate($authorsQuery, $request->query->getInt('page', 1), 15);

        $undefinedCount = $this->getDoctrine()->getRepository(Quote::class)->countQuotesForUndefinedAuthor($user);

        return $this->render('Inside/Author/index.html.twig', array('authors' => $authors, 'undefined' => $undefinedCount));
    }

    /**
     * @Route("/{id}/quotes", name="qtf_author_quotes", requirements={"id" = "\d+"})
     */
    public function listQuotes($id, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $author = $this->getDoctrine()->getRepository(Author::class)->getAuthorById($id, $user);

        if (null == $author) {
            $translated = $translator->trans('Oops! Something went wrong. The author could not be found.');
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_author_index');
        } else {
            $user = $this->getUser();
            $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryFindAllByAuthor($author, $user);
            $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);

            $translated = $translator->trans('All quotes for ');
            $displayTitle = $translated.$author->getDisplayName();

            return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
        }
    }

    /**
     * @Route("/undefined/quotes", name="qtf_author_quotes_undefined")
     */
    public function listUndefinedQuotes(Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $quotesQuery = $this->getDoctrine()->getRepository(Quote::class)->createQueryGetQuotesForUndefinedAuthor($user);
        $quotes = $paginator->paginate($quotesQuery, $request->query->getInt('page', 1), 10);
        $translated = $translator->trans('All anonymous quotes ');
        $displayTitle = $translated;

        return $this->render('Inside/Quote/index.html.twig', ['quotes' => $quotes, 'displayTitle' => $displayTitle]);
    }

    /**
     * @Route("/create_{caller}", name="qtf_author_create")
     */
    public function create($caller = 'qtf_quote_index', Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $author = new Author();
        $author->setUser($user);

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $translated = $translator->trans('The author has been added.');
            $this->addFlash('success', $translated);

            return $this->redirectToRoute($caller);
        }

        return $this->render('Inside/Author/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
    }

    /**
     * @Route("/{id}/edit_{caller}", name="qtf_author_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, $caller, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $author = $this->getDoctrine()->getRepository(Author::class)->getAuthorById($id, $user);

        if (null == $author) {
            $translated = $translator->trans('Oops! Something went wrong. The author could not be found.');
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_author_index');
        } else {
            $form = $this->createForm(AuthorType::class, $author);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($author);
                $em->flush();

                $translated = $translator->trans('The author has been modified.');
                $this->addFlash('success', $translated);

                return $this->redirectToRoute($caller);
            }

            return $this->render('Inside/Author/form.html.twig', ['form' => $form->createView(), 'previousPage' => $caller]);
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_author_delete", requirements={"id" = "\d+"})
     */
    public function delete($id, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $author = $this->getDoctrine()->getRepository(Author::class)->getAuthorById($id, $user);

        if (null == $author) {
            $translated = $translator->trans('Oops! Something went wrong. The author could not be found.');
            $this->addFlash('error', $translated);
        } elseif (count($author->getQuotes()) > 0) {
            $translated = $translator->trans('This author can not be deleted : it references quotes in the database.');
            $this->addFlash('warning', $translated);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();
            $translated = $translator->trans('The author has been deleted.');
            $this->addFlash('success', $translated);
        }

        return $this->redirectToRoute('qtf_author_index');
    }
}
