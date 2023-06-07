<?php

namespace App\Controller;

use app\Entity\Film;
use App\Form\EventType;
use App\Entity\Evenement;
use App\Form\EventwihtoutPictureType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event")
     */
    public function Event(ManagerRegistry $doctrine)
    {
        $repo = $doctrine->getRepository(Evenement::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $event = $repo->findby(array(), array('date' => 'asc'));
        } else {
            $event = $repo->eventAfterToday();
        }

        return $this->render('event/event.html.twig', [
            'event' => $event,
            'active_tab' => "event",
        ]);
    }

    /**
     * 
     * @Route("/evdetail/{id}", name="evdetail")
     */
    public function EvDetail(ManagerRegistry $doctrine, $id)
    {
        $repo = $doctrine->getRepository(Evenement::class);
        $event = $repo->find($id);
        $movies = $doctrine->getRepository(Film::class)->moviePerEvent($event);
        if (!$event) {
            return $this->redirectToRoute('event');
        }
        return $this->render('event/evdetail.html.twig', [
            'event' => $event,
            'active_tab' => "event",
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/event/new", name="event_create")
     * @Route("/event/{id}/edit", name="event-edit")
     */
    public function form(ManagerRegistry $doctrine, Evenement $event = null, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $doctrine->getManager();

        if (!$event) {
            $event = new Evenement();
        }

        if ($event->getImageName() == null) {
            $form = $this->createForm(EventwihtoutPictureType::class, $event);
        } else {
            $form = $this->createForm(EventType::class, $event);
        }

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($event);
            $manager->flush();
            return $this->redirectToRoute('event');
        }

        return $this->render('event/create.html.twig', [
            'formEvent' => $form->createView(),
            'active_tab' => "",
            'event' => $event,
            'editMode' => $event->getId() !== null,
            'eventFileName' => $event->getImageName()
        ]);
    }
    /**
     * @Route("/event/{id}/sup", name="event-remove")
     */
    public function eventRemove(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Evenement::class);
        $event = $repo->find($id);

        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('event');
    }
    /**
     * @Route("/event/{id}/supconfirm", name="event-remove-confrim")
     */
    public function eventRemoveconfirm(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $doctrine->getRepository(Evenement::class);
        $event = $repo->find($id);

        return $this->render('event/eventremove.html.twig', [
            'active_tab' => "",
            'event' => $event
        ]);
    }

    /**
     * @Route("/event/{id}/supimage", name="picture-remove")
     */
    public function pictureRemove(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $doctrine->getRepository(Evenement::class);
        $event = $repo->find($id);

        return $this->render('event/pictureremove.html.twig', [
            'active_tab' => "",
            'event' => $event
        ]);
    }
    /**
     * @Route("/event/{id}/supimageconf", name="picture-remove-confirm")
     */
    public function pictureRemoveConfirm(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Evenement::class);
        $event = $repo->find($id);
        $filename = $event->getImageName();

        \unlink('../public/images/event/' . $filename);

        $event->setImageName(null);

        $em->flush();

        return $this->redirectToRoute('event-edit', [
            'id' => $event->getId()
        ]);
    }
}
