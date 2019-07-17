<?php
// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPasswordType;
use Knp\Component\Pager\PaginatorInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/out/reset")
 */
class ResetPasswordController extends AbstractController
{

    // STEP 1 : display the form to enter email address
    /**
     * @Route("/email", name="qtf_reset_email")
     */
    public function email()
    {

        return $this->render('Outside/Reset/resetPassword_email.html.twig');

    }

    // STEP 2 : Check email validity in DB + send message with link to reset password
    /**
     * @Route("/message", name="qtf_reset_message")
     */
    public function message(Request $request, Swift_Mailer $mailer)
    {
        $email = $request->get('email');

        if (isset($email))
        {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($email);

            if ($user == null)
            {
                $this->addFlash('error', 'Email not found');
                return $this->redirectToRoute('qtf_reset_email');
            }
            else
            {
                // Send email to confirm address
                $message = (new Swift_Message('Quotify - Reset password'))
                    ->setFrom('no-reply@quotify-example.com')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('Outside/Emails/resetPassword.html.twig', ['user' => $user]), 'text/html');

                $mailer->send($message);

                return $this->render('Outside/Reset/confirmed1.html.twig');
            }
        }
        else
        {
            $this->addFlash('error', 'Please enter a valid email address.');
            return $this->redirectToRoute('qtf_reset_email');
        }

    }

    // STEP 3 : display form to reset password + reset password
    /**
     * @Route("/password", name="qtf_reset_password")
     */
    public function password(Request $request, Security $security, UserPasswordEncoderInterface $encoder)
    {
        $id = $request->get('id');
        $email = $request->get('email');
        $user = $this->getDoctrine()->getRepository(User::class)->getOne($id, $email);

        if ($user == null)
        {
            $this->addFlash('error', 'Oops! Something went wrong : user could not be found.');
            return $this->redirectToRoute('qtf_login');
        }
        else
        {
            $changePassword = new ChangePassword();
            $oldPassword = $user->getPassword();
            $changePassword->setOldPassword($oldPassword);

            $form = $this->createForm(ResetPasswordType::class, $changePassword);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {

                $newPasswordEntered = $form->getData()->getNewPassword();

                $encodedNewPassword = $encoder->encodePassword($user, $newPasswordEntered);
                $user->setPassword($encodedNewPassword);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'The password has been modified. You can now log-in');

                return $this->redirectToRoute('qtf_login');
            }

            return $this->render('Outside/Reset/resetPassword_password.html.twig', ['form' => $form->createView()]);
        }


    }


}