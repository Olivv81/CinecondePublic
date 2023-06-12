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
    public function index(Request $request, MailerInterface $mailer): response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            if (empty($contactFormData['age'])) {
                $message = (new \Swift_Message('message du site'))
                    ->setFrom('com.leroyalconde@gmx.fr')
                    ->setTo('com.leroyalconde@gmx.fr')
                    ->setCc('herve.cinema14@yahoo.fr')
                    ->setBody($contactFormData['message'] . '<br>' . $contactFormData['nom'] . '<br>' . $contactFormData['email'], 'text/html');



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
