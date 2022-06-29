<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{category}/show", name="category_show")
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/add", name="category_add")
     */
    public function add(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category     = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $categoryRepository->add($category, true);

            $this->addFlash('success', 'La catégorie a bien été créée !');

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/add.html.twig', [
            'category_form' => $categoryForm->createView()
        ]);
    }

    /**
     * @Route("/{category}/update", name="category_update")
     */
    public function update(Category $category, Request $request, CategoryRepository $categoryRepository): Response
    {
        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $categoryRepository->add($category, true);

            $this->addFlash('success', 'La catégorie a bien été mise à jour !');

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/update.html.twig', [
            'category_form' => $categoryForm->createView()
        ]);
    }
}
