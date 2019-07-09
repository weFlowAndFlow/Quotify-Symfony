<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Form\ChangePasswordType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/in/password")
 */
class ChangePasswordController extends AbstractController
{


    /**
     * @Route("/{id}/change", name="qtf_password_edit")
     */
    public function changePassword($id, Request $request, PaginatorInterface $paginator, Security $security, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $oldPassword = $user->getPassword();
        $changePassword->setOldPassword($oldPassword);

        if ($id == $user->getId()) {
            $form = $this->createForm(ChangePasswordType::class, $changePassword);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $oldPasswordEntered = $form->getData()->getOldPassword();
                $newPasswordEntered = $form->getData()->getNewPassword();

                if ($encoder->isPasswordValid($user, $oldPasswordEntered)) {
                    $encodedNewPassword = $encoder->encodePassword($user, $newPasswordEntered);
                    $user->setPassword($encodedNewPassword);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'The password has been modified.');

                    return $this->redirectToRoute('qtf_user_index');
                } else {
                    $this->addFlash('error', 'Oops! The old password is wrong.');
                }

            }

            return $this->render('Inside/User/passwordForm.html.twig', ['form' => $form->createView()]);

        } else {
            $this->addFlash('error', "Oops! There's been a problem : the user could not be found.");
            return $this->redirectToRoute('qtf_user_index');
        }


    }


}