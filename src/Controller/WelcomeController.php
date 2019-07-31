<?php

// src/Controller/WelcomeController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/out", requirements={
 *     "_locale"="%app.locales%"
 * }))
 */
class WelcomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="qtf_welcome_index")
     * @return Response
     */
    public function index()
    {
        return $this->render('Outside/index.html.twig');
    }

    /**
     * @Route("/sign-up", name="qtf_welcome_create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer, LoggerInterface $logger, TranslatorInterface $translator)
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

            $logger->alert('New user created', [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]);

            // Send email to confirm address
            $subject = $translator->trans('Confirm email address');
            $message = (new Swift_Message('Quotify - ' . $subject))
                ->setFrom('no-reply@quotify.weflowandflow.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('Outside/Emails/confirmAddress.html.twig', ['user' => $user]), 'text/html');

            $mailer->send($message);

            // Redirect to confirm account creation page
            return $this->render('Outside/Sign-up/confirmed.html.twig');
        }

        return $this->render('Outside/Sign-up/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/verify/51{id}84_{email}", name="qtf_welcome_verify", requirements={"id" = "\d+"})
     */
    public function verify(Request $request, TranslatorInterface $translator, LoggerInterface $logger)
    {
        $email = $request->get('email');
        $id = $request->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->getOne($id, $email);

        if (null == $user) {
            $translated = $translator->trans('Oops! Something went wrong. Please try to confirm your address again.');
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_welcome_index');
        } elseif ($user->isVerified()) {
            $translated = $translator->trans('Your email was already confirmed. You can log-in.');
            $this->addFlash('success', $translated);

            return $this->redirectToRoute('qtf_login');
        }

        $user->setVerified(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $logger->alert('User has been verified verified', [
            'id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        $translated = $translator->trans('Your email address has been successfully confirmed. You can now log-in.');
        $this->addFlash('success', $translated);

        return $this->redirectToRoute('qtf_login');
    }


}
