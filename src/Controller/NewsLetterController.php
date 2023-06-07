<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Horaire;
use App\Entity\ContactNL;
use App\Entity\Documents;
use App\Entity\Evenement;
use App\Entity\NewsLetter;
use App\Form\ContactNLType;
use App\Form\NewsLetterType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsLetterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="app_news_letter")
     */
    public function index(ManagerRegistry $doctrine, NewsLetter $newsLetter = null, Request $request, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $doctrine->getManager();
        $repoFilm = $doctrine->getRepository(Film::class);
        $repoEvent = $doctrine->getRepository(Evenement::class);
        $repoDoc = $doctrine->getRepository(Documents::class);

        $today = new \DateTime("now");

        $hor = $doctrine->getRepository(Horaire::class)->FilmByOneHoraire($today);
        $films = $repoFilm->getAfterToday($today);

        $movieTab = [];
        $nbHor = count($hor);
        for ($i = 0; $i < $nbHor; $i = $i + 1) {
            $film = $hor[$i]->getSeance()->getfilm()->getId();

            // dump("idh=" . $hor[$i]->getId() . " idf=" . $hor[$i]->getSeance()->getfilm()->getId());

            if (in_array($film, $movieTab)) {
                // dump("horsup" . $hor[$i]->getId());
                unset($hor[$i]);
            } else {
                array_push($movieTab, $film);
            }
        }

        $event = $repoEvent->eventAfterToday();

        if (!$newsLetter) {
            $newsLetter = new NewsLetter();
        }

        $form = $this->createForm(NewsLetterType::class, $newsLetter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($newsLetter);
            $manager->flush();
            dd($newsLetter);

            $NewsLetterFormData = $form->getData();
            $email = (new TemplatedEmail())
                ->from('***REMOVED***')
                ->to('***REMOVED***')
                ->subject('newsletter')

                // path of the Twig template to render
                ->htmlTemplate('news_letter/newsletter.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'films_aftertoday' => $films,
                    'titre' => $NewsLetterFormData['titre'],
                    'evenements' => $event,
                    'eventList' => $NewsLetterFormData['event'],
                    'movieList' => $NewsLetterFormData['films'],
                    'docs' => $NewsLetterFormData['docs'],
                    'messagebox' => $NewsLetterFormData['message'],

                ]);

            // $mailer->send($email);
            // return $this->redirectToRoute("confirm");
        }

        return $this->render('news_letter/NewsletterForm.html.twig', [
            'active_tab' => '',
            'NewsLetterForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/newsletter/inscritpion", name="CNLRegister")
     */
    public function CNLRegister(ManagerRegistry $doctrine, ContactNL $contactNL = null, Request $request)
    {
        $manager = $doctrine->getManager();
        $repoContact = $doctrine->getRepository(ContactNL::class);
        $contactNL = new ContactNL();

        $form = $this->createForm(ContactNLType::class, $contactNL);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dd($form);
            // dd(null !== $repoContact->findOneBy(['eMail' => $form['eMail']->getData()]));
            $today = new DateTime("now");
            $contactNL->setCreatedAt($today);
            $manager->persist($contactNL);
            $manager->flush();
            return $this->redirectToRoute('prog');
        }
        return $this->render('news_letter/NLContactForm.html.twig', [
            'NLContactForm' => $form->createView(),
            'active_tab' => "",
        ]);
    }
    /**
     * @Route("/test", name="test")
     */
    public function test(ManagerRegistry $doctrine, ContactNL $contactNL = null, Request $request)
    {
        $contacts = $doctrine->getRepository(ContactNL::class)->Destinataires(1);
        dd($contacts);
    }
}
