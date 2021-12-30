<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Seance;
use App\Entity\Horaire;
use App\Form\EventType;
use App\Entity\Evenement;
use App\Controller\Allocine;
use App\Form\EventwihtoutPictureType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProgController extends AbstractController
{
    /**
     * @Route("/", name="prog")
     */


    public function essai(): Response
    {
        $lesfilms = simplexml_load_file('../allocineseances.xml');
        $entityManager = $this->getDoctrine()->getManager();
        $repofilm = $this->getDoctrine()->getRepository(Film::class);
        $reposeance = $this->getDoctrine()->getRepository(Seance::class);
        $repohoraire = $this->getDoctrine()->getRepository(Horaire::class);


        require_once(__DIR__ . '/Allocine.php');

        // define('ALLOCINE_PARTNER_KEY', '100ED1DA33EB');
        // define('ALLOCINE_SECRET_KEY', '1a1ed8c1bed24d60ae3472eed1da33eb');



        function certificate($idfilm)
        {
            $allocine = new Allocine();
            $result = $allocine->get($idfilm);
            $movie = (json_decode($result, true));
            $test = empty($movie["movie"]['movieCertificate']['certificate']["$"]);

            if ($test == true) {
                $certif = "";
                return $certif;
            } else {
                $certif = $movie["movie"]['movieCertificate']['certificate']["$"];
                echo $certif;
                return  $certif;
            }
        }


        foreach ($lesfilms->xpath('//film') as $filmxml) {

            $filmexistant = $repofilm->findOneBy(['idFilm' => intval($filmxml['id'])]);

            // echo ($filmexistant . '<br/>');

            if (empty($filmexistant)) {
                // si le film en'existe pas, le créer :
                $film = new Film();
                $film
                    ->setIdFilm(intval($filmxml['id']))
                    ->setTitre($filmxml['titre'])
                    ->setRealisateurs($filmxml['realisateurs'])
                    ->setActeurs($filmxml['acteurs'])
                    ->setAnneeproduction(date_create_from_format('Y', $filmxml['anneeproduction']))
                    ->setDateSortie(date_create_from_format('d/m/Y', $filmxml['datesortie']))
                    ->setDuree(date_create_from_format('H\hi', $filmxml['duree']))
                    ->setGenrePrincipal($filmxml['genreprincipal'])
                    ->setNationalite($filmxml['nationalite'])
                    ->setSynopsis($filmxml['synopsis'])
                    ->setAffichette($filmxml['affichette'])
                    ->setAffichette250(str_replace(".fr/", ".fr/r_300_x", $filmxml['affichette']))
                    ->setVideo($filmxml['video'])
                    ->setVisaNumber(intval($filmxml['visanumber']))
                    ->setClassification(certificate(intval($filmxml['id'])));

                $entityManager->persist($film);
                $entityManager->flush();
            } else {
                $film = $filmexistant;
            }

            $IDfilm = $film->getId();

            foreach ($filmxml->horaire as $seancexml) {
                $projection = strval($seancexml['projection']);
                $VO = strval($seancexml['vo']);

                $seanceexistante = $this->getDoctrine()->getRepository(Seance::class)->findOneBy([
                    'projection' => $projection,
                    'vo' => $VO,
                    'film' => $IDfilm
                ]);


                if (empty($seanceexistante)) {
                    $seance = new Seance();

                    $seance
                        ->setVo($seancexml['vo'])
                        ->setVersion($seancexml['version'])
                        ->setProjection($seancexml['projection'])
                        ->setSoustitre($seancexml['soustitre'])
                        ->setFilm($film);
                    $film->addSeance($seance);
                    $entityManager->persist($seance);
                    $entityManager->flush();
                } else {
                    $seance = $seanceexistante;
                }

                $horairexml = explode(";", $seancexml);
                for ($line = 0; $line < count($horairexml); $line++) {
                    $horaireexistant = $repohoraire->FindOneBy([
                        'horaire' => (date_create_from_format('d-m-Y H:i:s', $horairexml[$line]))
                    ]);
                    if (empty($horaireexistant)) {

                        $horaire = new Horaire();
                        $horaire
                            ->setHoraire(date_create_from_format('d-m-Y H:i:s', $horairexml[$line]))
                            ->setSeance($seance);
                        $seance->addHoraire($horaire);
                        $entityManager->persist($horaire);
                        $entityManager->flush();
                    }
                }
            }


            $entityManager->flush();
        };


        $today = new \DateTime("now");
        // $film = 272014;
        // $prog = $this->getDoctrine()->getRepository(Horaire::class)->FilmDetail($film);
        $hor = $this->getDoctrine()->getRepository(Horaire::class)->FilmByOneHoraire($today);
        $activetab = "index";

        return $this->render('prog/index.html.twig', [
            // 'controller_name' => 'ProgController',
            'schedule_just_aftertoday' => $hor,
            'active_tab' => $activetab
        ]);
    }

    /**
     * @Route("/removall", name="removall")
     */
    public function removeallmovies()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $movies = $repo->findAll();
        foreach ($movies as $movies) {
            $firstmovie = $repo->findOneBy([]);
            $firstmovie = $manager->merge($firstmovie);
            $manager->remove($firstmovie);
            $manager->flush();
        }


        return $this->redirectToRoute('prog');
    }

    /**
     * @Route("/fdetail/{id}", name="fdetail")
     */

    public function FDetail($id)
    {
        $today = new \DateTime("now");
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $film = $repo->find($id);
        $horaires = $this->getDoctrine()->getRepository(Horaire::class)->schedulebymovie($film, $today);
        // $horaires = $this->getDoctrine()->getRepository(Horaire::class)->FilmDetail($id);;


        return $this->render('prog/fdetail.html.twig', [
            'film' => $film,
            'movie_schedule' => $horaires,
            'active_tab' => ""
        ]);
    }
    /**
     * @Route("/jour", name="jour")
     */
    public function dayFilter(Request $request)
    {
        $jour = $request->get('jour');

        $movie = $this->getDoctrine()->getRepository(Horaire::class)->movieByDay($jour);
        $event = $this->getDoctrine()->getRepository(Evenement::class)->eventByDay($jour);


        return $this->render('prog/jour.html.twig', [
            'event' => $event,
            'schedule' => $movie,
            'active_tab' => ""
        ]);
    }
    /**
     * @Route("/tarifs", name="tarifs")
     */
    public function Tarifs()
    {
        return $this->render('prog/tarifs.html.twig', [
            'active_tab' => "tarifs",
        ]);
    }
    /**
     * @Route("/aboutus", name="aboutus")
     */
    public function Aboutus()
    {
        return $this->render('aboutus.html.twig', [
            'active_tab' => "aboutus",
        ]);
    }
    /**
     * @Route("/download/{file}", name="download")
     */
    public function download($file)
    {
        switch ($file) {
            case "affichette":
                $filePath = '../affichette.pdf';
                break;
            case "programme":
                $filePath = '../prog.pdf';
                break;
        }


        $response = new BinaryFileResponse($filePath);
        return $response;
    }
    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function MentionsLegales()
    {
        return $this->render('prog/mentionslegale.html.twig', [
            'active_tab' => "",
        ]);
    }

    /**
     * @Route("/film/{id}/sup", name="movie-remove")
     */
    public function movieRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $movie = $repo->find($id);
        $em->remove($movie);
        $em->flush();

        return $this->redirectToRoute('prog');
    }

    /**
     * @Route("/event", name="event")
     */
    public function Event()
    {
        $repo = $this->getDoctrine()->getRepository(Evenement::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $event = $repo->findby(array(), array('date' => 'asc'));
        } else {
            $event = $repo->eventAfterToday();
        }

        return $this->render('prog/event.html.twig', [
            'event' => $event,
            'active_tab' => "event",
        ]);
    }

    /**
     * @Route("/evdetail/{id}", name="evdetail")
     */
    public function EvDetail($id)
    {
        $repo = $this->getDoctrine()->getRepository(Evenement::class);
        $event = $repo->find($id);
        $movies = $this->getDoctrine()->getRepository(Film::class)->moviePerEvent($event);


        return $this->render('prog/evdetail.html.twig', [
            'event' => $event,
            'active_tab' => "event",
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/event/new", name="event_create")
     * @Route("/event/{id}/edit", name="event-edit")
     */
    public function form(Evenement $event = null, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $this->getDoctrine()->getManager();

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

        return $this->render('prog/create.html.twig', [
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
    public function eventRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Evenement::class);
        $event = $repo->find($id);

        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('event');
    }
    /**
     * @Route("/event/{id}/supconfirm", name="event-remove-confrim")
     */
    public function eventRemoveconfirm($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $this->getDoctrine()->getRepository(Evenement::class);
        $event = $repo->find($id);

        return $this->render('prog/eventremove.html.twig', [
            'active_tab' => "",
            'event' => $event
        ]);
    }

    /**
     * @Route("/event/{id}/supimage", name="picture-remove")
     */
    public function pictureRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $this->getDoctrine()->getRepository(Evenement::class);
        $event = $repo->find($id);

        return $this->render('prog/pictureremove.html.twig', [
            'active_tab' => "",
            'event' => $event
        ]);
    }
    /**
     * @Route("/event/{id}/supimageconf", name="picture-remove-confirm")
     */
    public function pictureRemoveConfirm($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Evenement::class);
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
