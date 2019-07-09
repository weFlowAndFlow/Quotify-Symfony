<?php
// src/Controller/WelcomeController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/out")
 */
class WelcomeController extends AbstractController
{

    /**
     * @Route("/welcome", name="qtf_welcome_index")
     */
    public function index(Request $request)
    {
        return $this->render('Outside/index.html.twig');
    }

    /**
     * @Route("/sign-up", name="qtf_welcome_create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->getData()->getPassword();
            $encodedPassword = $encoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $user->setVerified(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Send email to confirm address
            $message = (new Swift_Message('Quotify - Confirm email address'))
                ->setFrom('no-reply@quotify-example.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('Outside/Emails/confirmAddress.html.twig', ['user' => $user]), 'text/html');

            $mailer->send($message);


            // Redirect to confirm account creation page
            return $this->render('Outside/Sign-up/confirmed.html.twig');

        }

        return $this->render('Outside/Sign-up/Form.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/verify/51{id}84_{email}", name="qtf_welcome_verify", requirements={"id" = "\d+"})
     */
    public function verify(Request $request)
    {
        $email = $request->get('email');
        $id = $request->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->getOne($id, $email);

        if ($user == null) {
            $this->addFlash('error', 'Oops! Something went wrong. Please try to confirm your address again.');
            return $this->redirectToRoute('qtf_welcome_index');
        } else if ($user->isVerified()) {
            $this->addFlash('success', 'Your email was already confirmed. You can log-in.');
            return $this->redirectToRoute('qtf_login');
        }
        {
            $user->setVerified(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your email address has been successfully confirmed. You can now log-in.');
            return $this->redirectToRoute('qtf_login');
        }


    }


}
