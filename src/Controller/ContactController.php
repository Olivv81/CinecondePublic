<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            if (empty($contactFormData['age']) && (strpos($contactFormData['message'], "http") == false) && (strpos($contactFormData['message'], "@Crypt") == false)) {

                $message = (new Email())
                    ->from('***REMOVED***')
                    ->to('***REMOVED***')
                    // ->setCc('***REMOVED***')
                    ->subject('Message venant du site !')
                    ->html(nl2br($contactFormData['message'] . '<br><br>' . $contactFormData['nom'] . '<br>' . $contactFormData['email']));

                $mailer->send($message);
                $this->addFlash('success', 'Votre message a été envoyé');
                return $this->redirectToRoute("confirm");
            }
            return $this->redirectToRoute("confirm");
        }
        return $this->render('contact/contactForm.html.twig', [
            'active_tab' => 'contact',
            'formContact' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirm()
    {
        return $this->render('contact/confirmation.html.twig', [
            'active_tab' => '',
        ]);
    }
}
