<?php

namespace App\Command;

use App\Entity\Film;
use App\Entity\Horaire;
use App\Entity\Evenement;
use App\Entity\NewsLetter;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    protected static $defaultName = 'app:send';

    private $mailer;

    public function __construct(MailerInterface $mailer, ManagerRegistry $doctrine)
    {
        parent::__construct(null);

        $this->mailer = $mailer;
        $this->doctrine = $doctrine;
    }

    protected static $defaultDescription = 'Envoyer la Newsletter';

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $today = new \DateTime("now");

        $doctrine = $this->doctrine;
        $repoEvent = $doctrine->getRepository(Evenement::class);
        $repoFilm = $doctrine->getRepository(Film::class);
        $repoNewsLetter = $doctrine->getRepository(NewsLetter::class);
        $films = $repoFilm->getAfterToday($today);
        $hor = $doctrine->getRepository(Horaire::class)->FilmByOneHoraire($today);
        $event = $repoEvent->eventAfterToday();

        foreach ($repoNewsLetter as $newsLetter) {

            $IDNewsletter = $newsLetter->getId();
            $contacts = $doctrine->getRepository(NewsLetter::class)->Destinataires($IDNewsletter);
            $email = (new TemplatedEmail())
                ->from('communication@cineconde.fr')
                ->to($contacts)
                ->subject('newsletter')

                // path of the Twig template to render
                ->htmlTemplate('news_letter/newsletter.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'films_aftertoday' => $films,
                    'titre' => $newsLetter->getTitre(),
                    'evenements' => $event,
                    'eventList' => $newsLetter->isEvent(),
                    'movieList' => $newsLetter->isFilms(),
                    'docs' => $newsLetter->isDocs(),
                    'messagebox' => $newsLetter->getMessage(),
                ]);
            $this->mailer->send($email);
        }




        // $email = (new Email())
        //     ->from('com.leroyalconde@gmx.fr')
        //     ->to('olivier.caillaud_gafsi@gmx.fr')
        //     ->subject('newsletter')
        //     ->text('Sending emails is fun again!');


        // path of the Twig template to render

        // pass variables (name => value) to the template



        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
