<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Quote;
use App\Entity\User;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormError;

/**
 * @Route("/in/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="qtf_user_index")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();


        return $this->render('Inside/User/index.html.twig');
    }

    /**
     * @Route("/{id}/edit", name="qtf_user_edit")
     */
    public function edit($id, Request $request, PaginatorInterface $paginator, Security $security, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        if ($id == $user->getId()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'The user has been modified.');

                    return $this->redirectToRoute('qtf_user_index');

            }
            return $this->render('Inside/User/form.html.twig', ['form' => $form->createView()]);
        } else {
            $this->addFlash('error', "Oops! There's been a problem : the user could not be found.");
            return $this->redirectToRoute('qtf_user_index');
        }


    }


}