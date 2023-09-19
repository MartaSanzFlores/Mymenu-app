<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recipes/list', name: 'app_recipe_list', methods:'GET')]
    public function index(RecipeRepository $recipeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $pagination = $paginator->paginate(
            $recipeRepository->paginationQuery(),
            $request->query->get('page', 1),
            5
        );

        $categories = $categoryRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination
        ]);
    }
}
