<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recipes/list', name: 'app_recipe_list', methods:['GET', 'POST'])]
    public function list(RecipeRepository $recipeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // creation de formulaire pour filtrage par nom de recette
        $filterForm = $this->createFormBuilder()
            ->add('name', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Ex: poulet, tarte, soupe...'
                )))
            // setMethod('GET') evite que le formulaire submit a requette POST quand on change de page apres avoir rempli le formulaire
            ->setMethod('GET')
            ->getForm();

        $filterForm->handleRequest($request);

        // si le formulaire est envoyé, on recupere les recettes qui correspondent à la recherche
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {

            // on recupere l'information validé du formulaire
            $submitName = $filterForm->getData()["name"];

            $pagination = $paginator->paginate(
                $recipeRepository->paginationfindByName($submitName),
                $request->query->get('page', 1),
                5
            );
        } else {
            // sinon on recupere tous les recettes
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
            'filterForm' => $filterForm
        ]);
    }

}
