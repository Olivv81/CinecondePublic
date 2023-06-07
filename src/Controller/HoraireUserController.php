<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use IntlDateFormatter;
use App\Entity\Horaire;
use App\Form\ProfilType;
use App\Form\PlanningType;
use App\Form\HoraireUserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HoraireUserController extends AbstractController
{
    /**
     * @Route("/profil/planning", name="planning")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $repoHoraire = $doctrine->getRepository(Horaire::class);
        $planning = $repoHoraire->HoraireAfterToday();
        $user = $this->getUser()->getId();
        $optionAdmin = $doctrine->getRepository(Admin::class)->find(1);

        return $this->render('horaire_user/planning.html.twig', [
            'horaire' => $planning,
            'userId' => $user,
            'optionAdmin' => $optionAdmin,
        ]);
    }
    /**
     * @Route("/profil/minscrire/{id}/{commission}", name="minscrire")
     */
    public function minscrire(ManagerRegistry $doctrine, $id, $commission)
    {
        $manager = $doctrine->getManager();
        $user = $this->getUser();
        $horaire = $doctrine->getRepository(Horaire::class)->find($id);

        if ($commission == 'accueil') {
            $userlist = $horaire->getAccueil();

            if (in_array($user, $userlist)) {
                return $this->redirectToRoute('planning');
                exit;
            }

            if (count($userlist) == 2) {
                $message = "Pas de chance ! Une autre personne viens juste de s'inscrire avant votre validation";
                $pathRetour = 'planning';
                return $this->render('horaire_user/message.html.twig', [
                    'pathRetour' => $pathRetour,
                    'message' => $message
                ]);
                exit;
            }

            if ($userlist == null) {
                $userlist = [];
            }
            array_push($userlist, $user);
            $userlist = array_values($userlist);
            $horaire->setAccueil($userlist);
        }

        if ($commission == 'projection') {
            $userlist = $horaire->getProjection();

            if (in_array($user, $userlist)) {
                return $this->redirectToRoute('planning');
                exit;
            }

            if (count($userlist) == 2) {
                $message = "Pas de chance ! Une autre personne viens juste de s'inscrire avant votre validation";
                $pathRetour = 'planning';
                return $this->render('horaire_user/message.html.twig', [
                    'pathRetour' => $pathRetour,
                    'message' => $message
                ]);
                exit;
            }
            if ($userlist == null) {
                $userlist = [];
            }
            \array_push($userlist, $user);
            $userlist = array_values($userlist);
            $horaire->setProjection($userlist);
        }
        // if (empty($Userlist)) {
        //     $Userlist = [];
        // };

        $manager->persist($horaire);
        $manager->flush();

        return $this->redirectToRoute('planning');
    }
    /**
     * @Route("/profil/medesinscrire/{id}/{commission}", name="medesinscrire")
     */
    public function medesinscrire(ManagerRegistry $doctrine, $id, $commission)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $manager = $doctrine->getManager();
        $user = $this->getUser();
        $horaire = $doctrine->getRepository(Horaire::class)->find($id);


        if ($commission == 'accueil') {
            $userlist = $horaire->getAccueil();
            $key = (\array_search($user, $userlist));
            unset($userlist[$key]);
            $userlist = \array_values($userlist);
            $horaire->setAccueil($userlist);
        }

        if ($commission == 'projection') {
            $userlist = $horaire->getProjection();
            $key = (\array_search($user, $userlist));
            unset($userlist[$key]);
            $userlist = \array_values($userlist);
            $horaire->setProjection($userlist);
        }

        // if (empty($Userlist)) {
        //     $Userlist = [];
        // };

        $manager->persist($horaire);
        $manager->flush();

        return $this->redirectToRoute('planning');
    }
    /**
     * @Route("/admin/desinscrire/{idhoraire}/{iduser}/{equipe}", name="desinscrire")
     */
    public function desinscrire(ManagerRegistry $doctrine, $idhoraire, $iduser, $equipe)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($iduser);
        $horaire = $doctrine->getRepository(Horaire::class)->find($idhoraire);

        if ($equipe == "accueil") {
            $userlist = $horaire->getAccueil();
            $key = (\array_search($user, $userlist));
            unset($userlist[$key]);
            $userlist = \array_values($userlist);
            $horaire->setAccueil($userlist);
        }
        if ($equipe == "projection") {
            $userlist = $horaire->getProjection();
            $key = (\array_search($user, $userlist));
            unset($userlist[$key]);
            $userlist = \array_values($userlist);
            $horaire->setProjection($userlist);
        }



        $manager->persist($horaire);
        $manager->flush();

        return $this->redirectToRoute('planningform', [
            'team' => $equipe
        ]);
    }

    /**
     * @Route("/admin/planningform/{team}", name="planningform")
     */
    public function planningform(ManagerRegistry $doctrine, $team, Request $request)
    {

        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository(Horaire::class);
        $usersAccueil = $doctrine->getRepository(User::class)->findby(['accueil' => "1"], ['prenom' => 'ASC']);

        $usersProjection = $doctrine->getRepository(User::class)->findby(['projection' => true], ['prenom' => 'ASC']);
        $planning = $repo->HoraireAfterToday();

        return $this->render('horaire_user/planningform.html.twig', [
            'horaire' => $planning,
            'usersAccueil' => $usersAccueil,
            'usersProjection' => $usersProjection,
            'team' => $team
        ]);
    }

    /**
     * @Route("/admin/submitplanning/{team}", name="submitplanning", methods="POST")
     */
    public function submitplanning(ManagerRegistry $doctrine, $team)
    {
        $manager = $doctrine->getManager();

        $repoHoraire = $doctrine->getRepository(Horaire::class);
        $repoUser = $doctrine->getRepository(User::class);
        $interferences = [];


        foreach ($_POST as $idHoraire => $equipe) {

            $horaire = $repoHoraire->find($idHoraire);
            $accueilList = $horaire->getAccueil();
            $projectionList = $horaire->getProjection();

            if (isset($equipe['accueil'])) {
                $accueilBenevoles = $equipe['accueil'];
                foreach ($accueilBenevoles as $idBenevole) {
                    if (!empty($idBenevole)) {
                        $user = $repoUser->find($idBenevole);
                        if (count($accueilList) == 2) {
                            array_push(
                                $interferences,
                                [
                                    'seance' => $horaire->getHoraire()->format("Y-m-d H:i"),
                                    'utilisateur' => $user->getPrenom(),
                                    "poste" => "accueil"
                                ]
                            );
                        } else {
                            \array_push($accueilList, $user);
                            $accueilList = \array_values($accueilList);
                        }
                    }
                }
                $horaire->setAccueil($accueilList);
            }

            if (isset($equipe['projection'])) {
                $projectionBenevoles = $equipe['projection'];
                foreach ($projectionBenevoles as $idBenevole) {
                    if (!empty($idBenevole)) {

                        $user = $repoUser->find($idBenevole);
                        if (count($projectionList) == 2) {
                            array_push(
                                $interferences,
                                [
                                    'seance' => $horaire->getHoraire()->format("Y-m-d H:i"),
                                    'utilisateur' => $user->getPrenom(),
                                    "poste" => "accueil"
                                ]
                            );
                        } else {
                            array_push($projectionList, $user);
                            $projectionList = \array_values($projectionList);
                        }
                    }
                }
                $horaire->setProjection($projectionList);
            }
        }

        $manager->persist($horaire);
        $manager->flush();

        setlocale(LC_TIME, 'fr_FR.utf-8', 'fra');
        date_default_timezone_set('Europe/Paris');

        if (!empty($interferences)) {
            $message = "Une personne s'est inscrite à certaines séances avant que vous ayez validé le planning.\r Les inscriptions suivantes n'ont pas été prises en compte :\r";
            foreach ($interferences as $interference) {
                $date = utf8_encode(strftime('%A %d %B %H:%M', strtotime($interference['seance'])));
                $message .= "\r" . $interference['utilisateur'] . " à la séance du " . $date;
            }

            $pathRetour = "planningform";
            return $this->render('horaire_user/message.html.twig', [
                'message' => $message,
                'pathRetour' => $pathRetour,
            ]);
        } else {
            return $this->redirectToRoute('planningform', [
                'team' => $team
            ]);
        }
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profil(ManagerRegistry $doctrine, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $manager = $doctrine->getManager();
        $horaires = $doctrine->getRepository(Horaire::class)->findAll();
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($user);
            $manager->flush();

            foreach ($horaires as $horaire) {

                foreach ($horaire->getAccueil() as $key => $benevole) {
                    if ($user->getId() == $benevole->getId()) {
                        $AccueilEquipe = array_replace($horaire->getAccueil(), array($key => $user));
                        $horaire->setAccueil($AccueilEquipe);
                    }
                }

                foreach ($horaire->getProjection() as $key => $benevole) {
                    if ($user->getId() == $benevole->getId()) {
                        $ProjectionEquipe = array_replace($horaire->getProjection(), array($key => $user));
                        $horaire->setProjection($ProjectionEquipe);
                    }
                }

                $manager->persist($horaire);
                $manager->flush();
            }


            $manager->flush();
            return $this->redirectToRoute('planning');
        }

        return $this->render('horaire_user/profiletype.html.twig', [
            'formProfil' => $form->createView(),
        ]);
    }
}
