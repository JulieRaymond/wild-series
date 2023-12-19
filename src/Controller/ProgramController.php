<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\Comment;
use App\Form\CommentType;

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

    #[Route('/{slug}/edit', name: 'app_program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        // Ajoute la vérification de propriété du programme
        if ($this->getUser() !== $program->getOwner()) {
            throw $this->createAccessDeniedException('Seul le propriétaire peut modifier la série !');
        }
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Message Flash de succès
            $this->addFlash('success', 'Bravo ! La série a été éditée avec succès.');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_program_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getSlug(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();

            // Message Flash de danger
            $this->addFlash('danger', 'La série a bien été supprimée.');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{slug}/season/{seasonId}/episode/{episodeSlug}', name: 'episode_show', methods: ['GET', 'POST'])]
    public function showEpisode(string $slug, int $seasonId, string $episodeSlug, ProgramRepository $programRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Security $security): Response
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


        //commentaire
        $comment = new Comment();
        $comment->setEpisode($episode);

        // Si l'utilisateur est connecté, renseignez automatiquement l'auteur
        if ($security->getUser()) {
            $comment->setAuthor($security->getUser());
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du formulaire
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succès.');
        }

        // Rendre la vue avec le formulaire de commentaire
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(Comment $comment, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur a le droit de supprimer le commentaire
        if (!($security->isGranted('ROLE_ADMIN') || ($security->isGranted('ROLE_CONTRIBUTOR') && $security->getUser() === $comment->getAuthor()))) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce commentaire.');
        }

        // Supprimer le commentaire de la base de données
        $entityManager->remove($comment);
        $entityManager->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Le commentaire a bien été supprimé.');

        // Rediriger vers la page du programme associé à l'épisode
        $programSlug = $comment->getEpisode()->getSeason()->getProgram()->getSlug();
        return $this->redirectToRoute('program_show', ['slug' => $programSlug]);
    }
}
