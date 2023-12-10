<?php
/*
namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route('/{programId<\d+>}/season/{seasonId<\d+>}', name: 'season_show', methods: ['GET'])]
    public function showSeason(int $programId, int $seasonId, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }

        $season = $program->getSeasons()->filter(function ($season) use ($seasonId) {
            return $season->getId() === $seasonId;
        })->first();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : ' . $seasonId . ' found in program\'s seasons.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }
}*/

namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\ProgramDuration;

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

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Utilisation du SluggerInterface pour générer un slug à partir du titre
            $slug = $slugger->slug($program->getTitle())->lower();
            $slug = str_replace(' ', '-', $slug);
            $program->setSlug($slug);

            $entityManager->persist($program);
            $entityManager->flush();

            //message Flash de succès
            $this->addFlash('success', 'Bravo ! La série a été créée avec succès.');

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

    /*#[Route('/{programId<\d+>}/season/{seasonId<\d+>}', name: 'season_show', methods: ['GET'])]
    public function showSeason(int $programId, int $seasonId, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }

        $season = $program->getSeasons()->filter(function ($season) use ($seasonId) {
            return $season->getId() == $seasonId;
        })->first();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : ' . $seasonId . ' found in program\'s seasons.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }*/

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

    /*#[Route('/{programId<\d+>}/season/{seasonId<\d+>}/episode/{episodeId<\d+>}', name: 'episode_show', methods: ['GET'])]
    public function showEpisode(int $programId, int $seasonId, int $episodeId, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }

        $season = $program->getSeasons()->filter(function ($season) use ($seasonId) {
            return $season->getId() == $seasonId;
        })->first();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : ' . $seasonId . ' found in program\'s seasons.'
            );
        }

        $episode = $season->getEpisodes()->filter(function ($episode) use ($episodeId) {
            return $episode->getId() == $episodeId;
        })->first();

        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with id : ' . $episodeId . ' found in season\'s episodes.'
            );
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }*/

    #[Route('/{slug}/season/{seasonId}/episode/{episodeId}', name: 'episode_show', methods: ['GET'])]
    public function showEpisode(string $slug, int $seasonId, int $episodeId, ProgramRepository $programRepository): Response
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

        $episode = $season->getEpisodes()->filter(function ($episode) use ($episodeId) {
            return $episode->getId() == $episodeId;
        })->first();

        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with id: ' . $episodeId . ' found in season\'s episodes.'
            );
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }


}