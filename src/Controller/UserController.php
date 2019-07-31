<?php

// src/Controller/QuoteController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/{_locale}/in/user", requirements={
 *     "_locale"="%app.locales%"
 * }))
 * @IsGranted("ROLE_USER")
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
    public function edit($id, Request $request, Security $security, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        if ($id == $user->getId()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $translated = $translator->trans('The user has been modified.');
                $this->addFlash('success', $translated);

                return $this->redirectToRoute('qtf_user_index');
            }

            return $this->render('Inside/User/form.html.twig', ['form' => $form->createView()]);
        } else {
            $translated = $translator->trans("Oops! There's been a problem : the user could not be found.");
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_user_index');
        }
    }

    /**
     * @Route("/{id}/delete", name="qtf_user_delete")
     */
    public function delete($id, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        if ($id == $user->getId()) {
            // Invalidates the session (before deleting the current user) so that Symfony does not look for the logged-in current user
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();

            $userEntity = $this->getDoctrine()->getRepository(User::class)->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($userEntity);
            $em->flush();

            $logger->alert('User has been deleted', [
                'id' => $userEntity->getId(),
                'email' => $userEntity->getEmail()
            ]);

            $translated = $translator->trans('Your account has been successfully deleted.');
            $this->addFlash('success', $translated);

            return $this->redirectToRoute('qtf_welcome_index');
        } else {
            $translated = $translator->trans("Oops! There's been a problem : the account could not be found.");
            $this->addFlash('error', $translated);

            return $this->redirectToRoute('qtf_user_index');
        }
    }
}
