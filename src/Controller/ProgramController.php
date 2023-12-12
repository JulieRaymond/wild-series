<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du SluggerInterface pour générer un slug à partir du titre
            $slug = $slugger->slug($program->getTitle())->lower();
            $slug = str_replace([' ', '_'], '-', $slug);
            $program->setSlug($slug);

            $entityManager->persist($program);
            $entityManager->flush();

            // Message Flash de succès
            $this->addFlash('success', 'Bravo ! La série a été créée avec succès.');

            // Envoi un email lorsque la création de la série est bien valide
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('raymond.julie86@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            // Redirige vers la liste des programmes
            return $this->redirectToRoute('program_index');
        }

        // Rendre le formulaire
        return $this->render('program/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Program $program, ProgramDuration $programDuration): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'programDuration' => $programDuration->calculate($program),
        ]);
    }

    #[Route('/{slug}/season/{seasonId}', name: 'season_show', methods: ['GET'])]
    public function showSeason(string $slug, int $seasonId, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['slug' => $slug]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with slug: ' . $slug . ' found in program\'s table.'
            );
        }

        $season = $program->getSeasons()->filter(function ($season) use ($seasonId) {
            return $season->getId() == $seasonId;
        })->first();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id: ' . $seasonId . ' found in program\'s seasons.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    #[Route('/{slug}/season/{seasonId}/episode/{episodeSlug}', name: 'episode_show', methods: ['GET'])]
    public function showEpisode(string $slug, int $seasonId, string $episodeSlug, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        $program = $programRepository->findOneBy(['slug' => $slug]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with slug: ' . $slug . ' found in program\'s table.'
            );
        }

        $season = $program->getSeasons()->filter(function ($season) use ($seasonId) {
            return $season->getId() == $seasonId;
        })->first();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id: ' . $seasonId . ' found in program\'s seasons.'
            );
        }

        $episode = $season->getEpisodes()->filter(function ($episode) use ($episodeSlug) {
            return $episode->getSlug() == $episodeSlug;
        })->first();

        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with slug: ' . $episodeSlug . ' found in season\'s episodes.'
            );
        }

        // Utilisation du SluggerInterface pour générer un slug à partir du titre de l'épisode
        $episodeSlug = $slugger->slug($episode->getTitle())->lower();
        $episodeSlug = str_replace([' ', '_'], '-', $episodeSlug);
        $episode->setSlug($episodeSlug);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }

}
