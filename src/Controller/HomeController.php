<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {

        // TODO recette de la journée, pour l'instant on laisse une recette au hasard

        $recipes = $recipeRepository->findAll();
        $homeRecipe = $recipes[array_rand($recipes, 1)];

        return $this->render('home/index.html.twig', [
            'homeRecipe' => $homeRecipe
        ]);
    }
}
