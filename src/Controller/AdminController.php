<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Seance;
use App\Entity\Horaire;
use App\Form\AdminType;
use App\Form\RelanceType;
use App\Form\AdminUserType;
use App\Form\SeanceHorsAfficheType;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;

class AdminController extends AbstractController
{

    /**
     * @Route("/profil/home", name="home")
     */

    public function home(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $repoHoraire = $doctrine->getRepository(Horaire::class);
        $horaires = $repoHoraire->HoraireAfterToday();

        $mesSeancesAccueil = [];
        $mesSeancesProjection = [];
        $seanceAccueilPourvoir = null;
        $seanceProjectionPourvoir = null;
        $nbAccueilLibre = 0;
        $nbProjectionLibre = 0;

        foreach ($horaires as $horaire) {
            $accueil = $horaire->getAccueil();
            $projection = $horaire->getProjection();

            $nbPersAccueil = \count($accueil);
            if ($nbPersAccueil < 2) {
                $nbAccueilLibre =  $nbAccueilLibre + 2 - $nbPersAccueil;
            }
            $nbPersProj = \count($projection);
            if ($nbPersProj < 1) {
                $nbProjectionLibre = $nbProjectionLibre + 1 - $nbPersProj;
            }

            if (\in_array($user, $accueil)) {
                \array_push($mesSeancesAccueil, $horaire);
            }
            if (\in_array($user, $projection)) {
                \array_push($mesSeancesProjection, $horaire);
            }
            if (\count($accueil) < 2 && $seanceAccueilPourvoir == null) {
                $seanceAccueilPourvoir = $horaire;
            }
            if (\count($projection) < 1 && $seanceProjectionPourvoir == null) {
                $seanceProjectionPourvoir = $horaire;
            }
        }

        return $this->render('admin/indexbenevole.html.twig', [
            'mesSeancesAccueil' => $mesSeancesAccueil,
            'mesSeancesProjection' => $mesSeancesProjection,
            'seanceAccueilPourvoir' => $seanceAccueilPourvoir,
            'seanceProjectionPourvoir' => $seanceProjectionPourvoir,
            'nbPlaceAccueil' => $nbAccueilLibre,
            'nbPlaceProjection' => $nbProjectionLibre
        ]);
    }


    /**
     * @Route("/admin/benevoles", name="benevoles")
     */
    public function benevoles(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $manager = $doctrine->getManager();
        $benevoles = $doctrine->getRepository((User::class));
        $users = $doctrine->getRepository(User::class)->findBy([], ['prenom' => 'ASC']);

        // $response = file_get_contents('../benevoles.json');
        // $response = json_decode($response);


        // foreach ($response as $benevole) {


        //     $benevoleExistant = $benevoles->findOneBy(["email" => strtolower($benevole->email)]);

        //     if (!empty($benevoleExistant)) {
        //         $benevoleExistant
        //             ->setAccueil($benevole->accueil)
        //             ->setEmail($benevole->email)
        //             ->setNom($benevole->nom)
        //             ->setPrenom($benevole->prenom)
        //             ->setProjection($benevole->projection);
        //         $manager->persist($benevoleExistant);
        //     } else {
        //         $user = new User();
        //         $user
        //             ->setAccueil($benevole->accueil)
        //             ->setEmail($benevole->email)
        //             ->setNom($benevole->nom)
        //             ->setPrenom($benevole->prenom);
        //         $user->setPassword("Motdepassedefaut30112022");
        //         $hash = $passwordHasher->hashPassword($user, $user->getPassword());
        //         $user->setPassword($hash);
        //         $manager->persist($user);
        //     }

        //     $manager->flush();
        // }


        return $this->render('admin/userlist.html.twig', [
            'users' => $users,

        ]);
    }

    /**
     * @Route("/admin/newuser", name="newuser")
     * @Route("/admin/{id}/edituser", name="edituser")
     */
    public function form(ManagerRegistry $doctrine, User $user = null, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $doctrine->getManager();
        $horaires = $doctrine->getRepository(Horaire::class)->findAll();
        // dd($repo);
        if (!$user) {
            $user = new user();
            $user->setPassword("Motdepassedefaut30112022");
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
        }
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

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
            };

            return $this->redirectToRoute('benevoles');
        }

        return $this->render('admin/userform.html.twig', [
            'formUser' => $form->createView(),
            'user' => $user,
            'editMode' => $user->getId() !== null,
        ]);
    }

    /**
     * @Route("/admin/supprimeruser/{id}", name="supprimerUser")
     */
    public function supprimerUser(ManagerRegistry $doctrine, User $iduser)
    {
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository(Horaire::class);
        $horaires = $repo->HoraireAfterToday();
        $user = $doctrine->getRepository(User::class)->find($iduser);

        $manager->remove($user);
        $manager->flush();


        foreach ($horaires as $key => $horaire) {

            $AccueilBenevoles = $horaire->getAccueil();

            foreach ($AccueilBenevoles as $key => $benevole) {
                if ($user == $benevole) {
                    unset($AccueilBenevoles[$key]);
                    $horaire->setAccueil($AccueilBenevoles);
                    $manager->persist($horaire);
                }
            }
            $ProjectionBenevoles = $horaire->getProjection();
            foreach ($ProjectionBenevoles as $key => $benevole) {
                if ($user == $benevole) {
                    unset($ProjectionBenevoles[$key]);
                    $horaire->setProjection($ProjectionBenevoles);

                    $manager->persist($horaire);
                }
            }
        }
        $manager->flush();

        return $this->redirectToRoute('benevoles');
    }


    /**
     * @Route("/admin/controleur", name="controleur")
     */
    public function admincontroleur(ManagerRegistry $doctrine, Request $request)
    {

        $manager = $doctrine->getManager();
        $optionAdmin = $doctrine->getRepository(Admin::class)->find(1);
        $form = $this->createForm(AdminType::class, $optionAdmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $manager->persist($optionAdmin);
            $manager->flush();
            return $this->redirectToRoute('planning');
        }

        return $this->render('admin/controllerType.html.twig', [
            'option' => $optionAdmin,
            'formAdmin' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/relance", name="relance")
     */
    public function relance(ManagerRegistry $doctrine, Request $request, MailerInterface $mailer)
    {
        $user = $this->getUser();
        $accueilBenevoles = $doctrine->getRepository(User::class)->findBy(['accueil' => true]);
        $projectionBenevoles = $doctrine->getRepository(User::class)->findBy(['projection' => true]);
        $horaires = $doctrine->getRepository(Horaire::class)->HoraireAfterToday();
        $seancesAccueilLibres = [];
        $seancesProjectionLibres = [];

        foreach ($horaires as $horaire) {
            if (count($horaire->getAccueil()) < 2) {
                \array_push($seancesAccueilLibres, $horaire);
            }
            if (count($horaire->getProjection()) < 1) {
                \array_push($seancesProjectionLibres, $horaire);
            }
        }

        $form = $this->createForm(RelanceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $relanceFormData = $form->getData();
            $destinataires = [];
            $accueilBenevoleMail = [];
            $projectionBenevoleMails = [];

            if (in_array('accueil', $relanceFormData['commission'])) {
                foreach ($accueilBenevoles as $benevole) {
                    array_push($accueilBenevoleMail, $benevole->getEmail());
                }
            }

            if (in_array('projection', $relanceFormData['commission'])) {
                foreach ($projectionBenevoles as $benevole) {
                    \array_push($projectionBenevoleMails, $benevole->getEmail());
                }
            }

            $destinataires = array_merge($accueilBenevoleMail, $projectionBenevoleMails);
            $destinataires = array_unique($destinataires);
            $expediteur = $user->getEmail();

            $email = (new TemplatedEmail())
                ->from('communication@cineconde.fr')
                ->to(...$destinataires)
                // ->addto((...$destinataires))
                ->subject($relanceFormData['objet'])
                // ->text($relanceFormData['message']);
                // path of the Twig template to render
                ->htmlTemplate('admin/mailrelance.html.twig')
                // pass variables (name => value) to the template
                ->context([
                    'message' => $relanceFormData['message'],
                    'accueil' => in_array('accueil', $relanceFormData['commission']),
                    'projection' => in_array('projection', $relanceFormData['commission']),
                    'signature' => $user->getPrenom(),
                    'toutesLesSeances' => $relanceFormData['listeDesSeances'],
                    'horairesAccueil' => $seancesAccueilLibres,
                    'horairesProjection' => $seancesProjectionLibres,
                    'horaires' => $horaires,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('benevoles');
            // if ($relanceFormData['commission'] = true) {
            //     # code...
            // }
        }

        return $this->render('admin/relance.html.twig', [
            'formRelance' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/seancehorsaffiche", name="seancehorsaffiche")
     */
    public function seanceHorsAffiche(ManagerRegistry $doctrine)
    {
        $seancesHorsAffiche = $doctrine->getRepository(Horaire::class)->findBy(['aLAffiche' => false], ['horaire' => 'DESC']);
        return $this->render('admin/seancehorsaffiche.html.twig', [
            'horaires' => $seancesHorsAffiche
        ]);
    }

    /**
     * @Route("/admin/newseancehorsaffiche", name="newseancehorsaffiche")
     * @Route("/admin/{id}/editseancehorsaffiche", name="editseancehorsaffiche")
     */
    public function seancehorsafficheform(ManagerRegistry $doctrine, Horaire $horaire = null, Request $request)
    {

        $form = $this->createForm(SeanceHorsAfficheType::class, $horaire);
        $form->handleRequest($request);
        $manager = $doctrine->getManager();

        if (!$horaire) {
            $horaire = new Horaire();
        }

        $horaireFormData = $form->getData();
        if ($form->isSubmitted() && $form->isvalid()) {
            if (is_object($horaireFormData)) {
                $horaire = $horaireFormData;
            } else {
                $horaire->setALAffiche(false);
                $horaire->setTypeSeance($horaireFormData['typeSeance']);
                $horaire->setHoraire($horaireFormData['horaire']);
                // $horaire->setHoraire(date_create_from_format('d-m-Y H:i:s', $horaireFormData['horaire']));
            }

            $manager->persist($horaire);
            $manager->flush();
            return $this->redirectToRoute('seancehorsaffiche');
        }

        return $this->render('admin/seancehorsafficheform.html.twig', [
            'formHoraire' => $form->createView(),
            'horaire' => $horaire,
            'editMode' => $horaire->getId() !== null,
        ]);
    }

    /**
     * @Route("/admin/supprimerseancehorsaffiche/{id}", name="supprimerseancehorsaffiche")
     */
    public function supprimerseancehorsafficher(ManagerRegistry $doctrine, Horaire $horaire)
    {
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository(Horaire::class);
        // $user = $doctrine->getRepository(User::class)->find($iduser);


        $manager->remove($horaire);
        $manager->flush();
        return $this->redirectToRoute('seancehorsaffiche');
    }
}
