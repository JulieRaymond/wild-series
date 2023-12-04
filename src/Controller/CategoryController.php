<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Form\CategoryType;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->persist($category);
            $entityManager->flush();

            // Redirect to category list
            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            return $this->render('error/404.html.twig', [], new Response('', 404));
        }

        $programs = $programRepository->findBy(
            ['category' => $category],
            ['id' => 'DESC'],
            3 // Limit to 3 programs
        );

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs,
        ]);
    }
    #[Route('/show_categories_links/', name: 'show_categories_links', methods: ['GET'])]
    public function showCategoryLinks(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/_categories_links.html.twig', [
            'categories_links' => $categories,
        ]);
    }

}
