<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Seance;
use App\Entity\Horaire;
use App\Form\MovieType;
use App\Form\SeanceType;
use App\Entity\Evenement;
use App\Controller\Allocine;
use App\Form\MovieWithoutPictureType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\RememberMe\PersistentToken;
use Symfony\Component\Validator\Constraints\Length;

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
                        ->setVo(boolval(intval($seancexml['vo'])))
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

        $hor = $this->getDoctrine()->getRepository(Horaire::class)->FilmByOneHoraire($today);
        // $hor = $this->getDoctrine()->getRepository(Film::class)->getAfterToday($today);
        // dd($hor[1]->getSeance()->getId());


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
     * @Route("/film/new", name="movie-create")
     * @Route("/film/{id}/edit", name="movie-edit")
     */
    public function form(Film $film = null, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $this->getDoctrine()->getManager();

        if (!$film) {
            $film = new Film();
            $manager->persist($film);
        }

        if ($film->getImageName() == null) {
            $form = $this->createForm(MovieWithoutPictureType::class, $film);
        } else {
            $form = $this->createForm(MovieType::class, $film);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute("fdetail", ["id" => $film->getId()]);
        }

        return $this->render('prog/create.html.twig', [
            'formMovie' => $form->createView(),
            'active_tab' => "",
            'movie' => $film,
            'editMode' => $film->getId() !== null,
            'MovieFileName' => $film->getImageName()
        ]);
    }

    /**
     * @Route("/seance/{id}/edit/{createHor}", name="seance-edit")
     */
    public function formSeance(Seance $seance = null, Request $request, $createHor = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $this->getDoctrine()->getManager();


        if ($createHor == 1) {
            $hor = new Horaire;
            $hor->setSeance($seance);
            $seance->addHoraire($hor);
            $manager->persist($hor);
            $manager->persist($seance);
        }
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($seance);
            $manager->flush();

            $idf = $seance->getFilm()->getId();
            if ($form->get('addHoraire')->isClicked()) {

                return $this->redirectToRoute("seance-edit", ['createHor' => 1, 'id' => $seance->getId()]);
            } else {

                return $this->redirectToRoute('fdetail', ['id' => $idf]);
            }
        }

        return $this->render('prog/editSeance.html.twig', [
            'formSeance' => $form->createView(),
            'active_tab' => "",
            'seance' => $seance,
        ]);
    }

    /**
     * @Route("/film/{idf}/newseance", name="seance-create")
     */
    public function formCreateSeance(Seance $seance = null, Request $request, $idf)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $film = $repo->find($idf);

        $seance = new Seance;
        $seance->setFilm($film);
        $seance->setVo(0);

        $hor = new Horaire;
        $hor->setSeance($seance);
        $seance->addHoraire($hor);

        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($hor);
            $manager->persist($seance);
            $manager->flush();


            if ($form->get('addHoraire')->isClicked()) {
                return $this->redirectToRoute("seance-edit", ['id' => $seance->getId(), "createHor" => 1]);
            } else {
                return $this->redirectToRoute('fdetail', ['id' => $idf]);
            }
        }
        return $this->render('prog/editSeance.html.twig', [
            'formSeance' => $form->createView(),
            'active_tab' => "",
            'seance' => $seance,
        ]);
    }


    /**
     * @Route("/seance/{id}/remove", name="seance-remove")
     */
    public function seanceRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Seance::class);
        $seance = $repo->find($id);
        $em->remove($seance);
        $em->flush();
        $idFilm = $seance->getfilm()->getId();

        return $this->redirectToRoute('fdetail', ['id' => $idFilm]);
    }

    /**
     * @Route("/horaire/{id}/remove", name="horaire-remove")
     */
    public function horaireRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Horaire::class);
        $horaire = $repo->find($id);
        $em->remove($horaire);
        $em->flush();
        $idFilm = $horaire->getSeance()->getfilm()->getId();

        return $this->redirectToRoute('fdetail', ['id' => $idFilm]);
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
     * @Route("/film/{id}/supimage", name="movie-picture-remove")
     */
    public function pictureRemove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $movie = $repo->find($id);

        return $this->render('prog/pictureremove.html.twig', [
            'active_tab' => "",
            'movie' => $movie
        ]);
    }

    /**
     * @Route("/film/{id}/supimageconf", name="movie-picture-remove-confirm")
     */
    public function pictureRemoveConfirm($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $movie = $repo->find($id);
        $filename = $movie->getImageName();

        \unlink('../public/images/film/' . $filename);

        $movie->setImageName(null);

        $em->flush();

        return $this->redirectToRoute('movie-edit', [
            'id' => $movie->getId()
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
}
