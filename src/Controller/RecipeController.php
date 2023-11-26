<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Form\RecipeType;
use Doctrine\ORM\EntityRepository;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recipes/list', name: 'app_recipe_list', methods: ['GET', 'POST'])]
    public function list( RecipeRepository $recipeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response 
    {

        // Création du formulaire pour filtrage par nom de recette
        $filterForm = $this->createForm(RecipeType::class);
        $filterForm->handleRequest($request);

        // Création du formulaire pour filtrage par catégorie
        $categoryFilterForm = $this->createFormBuilder()
            ->add('Category', EntityType::class, [
                'placeholder' => '-',
                'label' => 'Choisir une catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $et) {
                    return $et->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->setMethod('GET')
            ->getForm();

        $categoryFilterForm->handleRequest($request);

        // Initialiser la pagination en dehors des conditions
        $pagination = null;

        // Si le formulaire de filtrage par nom est soumis et valide
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $submitName = $filterForm->get('name')->getData();

            $pagination = $paginator->paginate(
                $recipeRepository->paginationfindByName($submitName),
                $request->query->get('page', 1),
                5
            );
        }
        // Si le formulaire de filtrage par catégorie est soumis et valide
        elseif ($categoryFilterForm->isSubmitted() && $categoryFilterForm->isValid() && $categoryFilterForm->get('Category')->getData() !== null) {
            $selectedCategory = $categoryFilterForm->get('Category')->getData();

            $pagination = $paginator->paginate(
                $selectedCategory->getRecipes(),
                $request->query->get('page', 1),
                5
            );
            
        } else {
            // Sinon, on récupère toutes les recettes
            $pagination = $paginator->paginate(
                $recipeRepository->paginationQuery(),
                $request->query->get('page', 1),
                5
            );
        }

        $categories = $categoryRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
            'categoryFilterForm' => $categoryFilterForm->createView(),
        ]);
    }

    #[Route('/recipes/{id}', name: 'app_recipe_show', methods: ['GET'])]
    public function show( #[MapEntity(id: 'id')]
    Recipe $recipe): Response 
    {

        $ingredients = $recipe->getIngredients();
        $steps = $recipe->getSteps();

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'ingredients' => $ingredients,
            'steps' => $steps
        ]);
    }

    #[Route('/recipes/new', name: 'app_recipe_new', methods: ['GET'])]
    public function new(): Response 
    {
        $recipe = new Recipe();


       

        return $this->render('recipe/show.html.twig', [
        ]);
    }
}

