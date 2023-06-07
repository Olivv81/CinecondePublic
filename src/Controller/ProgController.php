<?php

namespace App\Controller;

use DateTime;
use App\Entity\Film;
use App\Entity\Horaire;
use App\Form\MovieType;
use App\Form\SeanceType;
use App\Entity\Documents;
use App\Entity\Evenement;
use App\Controller\Allocine;
use App\Form\MovieWithoutPictureType;
use SebastianBergmann\Timer\Duration;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ProgController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */

    public function sitemap()
    {
        $response = new Response(
            $this->renderView('/sitemap.html.twig')
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * @Route("/", name="prog")
     */


    public function essai(ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();
        $repofilm = $doctrine->getRepository(Film::class);
        $repohoraire = $doctrine->getRepository(Horaire::class);


        function findTrailerInTMDB($titre, $year)
        {
            $keyTMDB = "***REMOVED***";
            $url = 'https://api.themoviedb.org/3/search/movie?api_key=' . $keyTMDB . '&query=' . $titre . '&year=' . $year . '&language=fr-FR&page=1&include_adult=false';

            $encodedUrl = str_replace(" ", "%20", $url);
            $json = file_get_contents($encodedUrl);

            $movieTMDB = json_decode($json, true);

            if (empty($movieTMDB['results'][0]['id'])) {
                return null;
            }

            $MovieIdTMDB = $movieTMDB['results'][0]['id'];
            $url = "https://api.themoviedb.org/3/movie/{$MovieIdTMDB}/videos?api_key={$keyTMDB}&language=fr-FR";

            $json = file_get_contents($url);
            $trailerTMDB = json_decode($json, true);

            if (empty($trailerTMDB['results'][0]['key'])) {
                return null;
            }

            return $trailerTMDB['results'][0]['key'];
        }
        // $titre = 'Don Juan';
        // // $titreMo = \str_replace(" ", "+", $titre);
        // findTrailerInTMDB($titre, "2022-05-23");

        // require_once(__DIR__ . '/Allocine.php');

        // define('ALLOCINE_PARTNER_KEY', '100ED1DA33EB');
        // define('ALLOCINE_SECRET_KEY', '1a1ed8c1bed24d60ae3472eed1da33eb');


        function classification($codeAPI)
        {

            $code = preg_replace('/[0-9]/', '', $codeAPI);
            $age = preg_replace('/[^0-9]/', '', $codeAPI);
            if ($code == 'ALL') {
                return 'Tout public';
            } elseif ($age === "" && $code == 'A') {
                return 'Avertissement';
            } elseif ($code == 'A') {
                return 'Avertissement au moins de ' . $age . ' ans';
            } elseif ($code == 'R') {
                return 'Interdiction au moins de ' . $age . ' ans';
            }
        };

        // https://movies.monnaie-services.com/doc/fr/prog_api
        $response = file_get_contents('***REMOVED***');
        $response = json_decode($response);
        // visualisation du contenu de l'API :
        // dd(date('D d-m-Y H:i:s', $response->version), $response);

        $lesfilms = $response->sites[0]->events;


        foreach ($lesfilms as $filmAPI) {


            $filmexistant = $repofilm->findOneBy(['idFilm' => $filmAPI->id]);


            // echo ($filmexistant . '<br/>');

            if (empty($filmexistant)) {
                // si le film en'existe pas, le créer :             
                $film = new Film();
                // préparation de la syntaxe pour le query de l'API TMDB
                $titre = $filmAPI->title;
                $year = (DateTime::createFromFormat('Ymd', $filmAPI->release_date))->format('Y');
                $videoYT = findTrailerInTMDB($titre, $year);

                if (isset($filmAPI->actors)) {
                    $film->setActeurs($filmAPI->actors);
                }

                if (isset($filmAPI->country)) {
                    $film->setNationalite(locale_get_display_region('-' . $filmAPI->country, 'fr-FR'));
                }
                if (isset($filmAPI->certification_id)) {
                    $film->setClassification(classification($filmAPI->certification_id));
                }

                $film
                    ->setIdFilm($filmAPI->id)
                    ->setTitre($filmAPI->title)
                    ->setRealisateurs($filmAPI->director)
                    ->setDateSortie(DateTime::createFromFormat('Ymd', $filmAPI->release_date))
                    ->setDuree($filmAPI->duration)
                    ->setGenrePrincipal($filmAPI->localized_genres)
                    ->setSynopsis($filmAPI->synopsis)
                    ->setAffichette(str_replace("/120/", "/600/", $filmAPI->bill_url))
                    ->setVideoYT($videoYT)
                    ->setAffichette250(str_replace("/120/", "/320/", $filmAPI->bill_url));
                // dd((date_format(date_create_from_format('d/m/Y', $filmxml['datesortie']), "Y")));

                // $entityManager->persist($film);
                // $entityManager->flush();
            } else {
                $film = $filmexistant;
            }

            $IDfilm = $film->getId();

            foreach ($filmAPI->sessions as $seanceAPI) {
                $IdApi = $seanceAPI->id;
                $seanceexistante = $doctrine->getRepository(Horaire::class)->findOneBy(['IdEMS' => $IdApi,]);

                if (empty($seanceexistante)) {

                    $horaire = new Horaire();

                    if (in_array("vo", $seanceAPI->features)) {
                        $horaire->setVo(1);
                    } else {
                        $horaire->setVo(0);
                    }

                    if (in_array("video_3d", $seanceAPI->features)) {
                        $horaire->setTroisD(1);
                    } else {
                        $horaire->setTroisD(0);
                    }

                    $horaire
                        ->setHoraire(DateTime::createFromFormat('YmdHi', $seanceAPI->date))
                        ->setFilm($film)
                        ->setALAffiche(true)
                        ->setIdEMS($IdApi);

                    $film->addHoraire($horaire);
                    $entityManager->persist($horaire);
                    $entityManager->persist($film);
                    $entityManager->flush();
                }

                if ($seanceexistante) {

                    if (in_array("vo", $seanceAPI->features)) {
                        $seanceexistante->setVo(1);
                    } else {
                        $seanceexistante->setVo(0);
                    }

                    if (in_array("video_3d", $seanceAPI->features)) {
                        $seanceexistante->setTroisD(1);
                    } else {
                        $seanceexistante->setTroisD(0);
                    }

                    $seanceexistante
                        ->setHoraire(DateTime::createFromFormat('YmdHi', $seanceAPI->date))
                        ->setFilm($film)
                        ->setALAffiche(true)
                        ->setIdEMS($IdApi);

                    $film->addHoraire($seanceexistante);
                    $entityManager->persist($seanceexistante);
                    $entityManager->persist($film);
                    $entityManager->flush();
                }
            }
            $entityManager->flush();
        };



        $today = new \DateTime("now");

        $hor = $doctrine->getRepository(Horaire::class)->FilmByOneHoraire($today);

        // dd($hor[1]->getSeance()->getId());


        $movieTab = [];
        $nbHor = count($hor);
        for ($i = 0; $i < $nbHor; $i = $i + 1) {
            $film = $hor[$i]->getFilm()->getId();



            if (in_array($film, $movieTab)) {

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
    public function removeallmovies(ManagerRegistry $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository(Film::class);
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

    public function FDetail(ManagerRegistry $doctrine, $id)
    {
        $today = new \DateTime('midnight');

        $repo = $doctrine->getRepository(Film::class);
        $film = $repo->find($id);

        if (!$film) {
            return $this->redirectToRoute("prog");
        }

        $horaires = $doctrine->getRepository(Horaire::class)->schedulebymovie($film, $today);




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
    public function form(ManagerRegistry $doctrine, Film $film = null, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $doctrine->getManager();

        if (!$film) {
            $film = new Film();
            // $manager->persist($film);
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
    public function formSeance(ManagerRegistry $doctrine, Seance $seance = null, Request $request, $createHor = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $doctrine->getManager();

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
            foreach ($seance->getHoraires() as $horaire) {
                $horaire->setALAffiche(true);
            }

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
    public function formCreateSeance(ManagerRegistry $doctrine, Seance $seance = null, Request $request, $idf)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository(Film::class);
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
            foreach ($seance->getHoraires() as $horaire) {
                $horaire->setALAffiche(true);
            }
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
     * @Route("/horaire/{id}/remove", name="horaire-remove")
     */
    public function horaireRemove(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Horaire::class);
        $horaire = $repo->find($id);
        $em->remove($horaire);
        $em->flush();


        return $this->redirectToRoute('prog');
    }


    /**
     * @Route("/jour", name="jour")
     */
    public function dayFilter(ManagerRegistry $doctrine, Request $request)
    {
        $jour = $request->get('jour');

        $movie = $doctrine->getRepository(Horaire::class)->movieByDay($jour);
        $event = $doctrine->getRepository(Evenement::class)->eventByDay($jour);


        return $this->render('prog/jour.html.twig', [
            'event' => $event,
            'schedule' => $movie,
            'active_tab' => ""
        ]);
    }
    /**
     * @Route("/film/{id}/supimage", name="movie-picture-remove")
     */
    public function pictureRemove(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $doctrine->getRepository(Film::class);
        $movie = $repo->find($id);

        return $this->render('prog/pictureremove.html.twig', [
            'active_tab' => "",
            'movie' => $movie
        ]);
    }

    /**
     * @Route("/film/{id}/supimageconf", name="movie-picture-remove-confirm")
     */
    public function pictureRemoveConfirm(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Film::class);
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
    public function movieRemove(ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Film::class);
        $movie = $repo->find($id);
        $em->remove($movie);
        $em->flush();

        return $this->redirectToRoute('prog');
    }
}
