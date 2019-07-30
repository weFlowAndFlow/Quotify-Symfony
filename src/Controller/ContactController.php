<?php

// src/Controller/ContactController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/contact", requirements={
 *     "_locale"="%app.locales%"
 * }))
 */
class ContactController extends AbstractController
{

    /**
     * @Route("/", name="qtf_contact")
     */
    public function contact(Request $request, Swift_Mailer $mailer)
    {
        $user = $this->getUser();

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $message = (new Swift_Message($request->get('object')))
                ->setFrom('contact@quotify.weflowandflow.com')
                ->setTo('contact@quotify.weflowandflow.com')
                ->setBody($this->renderView('Outside/Emails/message.html.twig', [
                    'name' => $form->get('name')->getData(),
                    'email' => $form->get('email')->getData(),
                    'object' => $form->get('object')->getData(),
                    'message' => $form->get('message')->getData()
                ]),
                    'text/html'
                );

            $mailer->send($message);

            // Display confirm page depending of user connection
            if ($user == null)
            {
                return $this->render('Outside/Contact/confirmation.html.twig');
            }
            else
            {
                return $this->render('Inside/Contact/confirmation.html.twig');
            }

        }
        // display form depending of user is connection
        if ($user == null)
        {
            return $this->render('Outside/Contact/form.html.twig', ['form' => $form->createView()]);
        }
        else
        {
            return $this->render('Inside/Contact/form.html.twig', ['form' => $form->createView()]);
        }
    }

}
