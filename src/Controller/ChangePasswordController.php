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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/in/password")
 */
class ChangePasswordController extends AbstractController
{


    /**
     * @Route("/{id}/change", name="qtf_password_edit")
     */
    public function changePassword($id, Request $request, TranslatorInterface $translator, Security $security, UserPasswordEncoderInterface $encoder)
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

                    $translated = $translator->trans('The password has been modified.');
                    $this->addFlash('success', $translated);

                    return $this->redirectToRoute('qtf_user_index');
                } else {
                    $translated = $translator->trans('Oops! The old password is wrong.');
                    $this->addFlash('error', $translated);
                }

            }

            return $this->render('Inside/User/passwordForm.html.twig', ['form' => $form->createView()]);

        } else {
            $translated = $translator->trans("Oops! There's been a problem : the user could not be found.");
            $this->addFlash('error', $translated);
            return $this->redirectToRoute('qtf_user_index');
        }

    }



}