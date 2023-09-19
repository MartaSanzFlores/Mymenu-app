<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Step;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\MyMenuProvider;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        // on crée une instance de Faker Generator
        $faker = Factory::create('fr_FR');

        // on donne notre provider à Faker
        $faker->addProvider(new MyMenuProvider());


        // on crée 3 Users "en dur" "statiques"
        // admin
        $userAdmin = new User();
        $userAdmin->setEmail('admin@gmail.com');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword('$2y$13$.PJiDK3kq2C4owW5RW6Z3ukzRc14TJZRPcMfXcCy9AyhhA9OMK3Li');
        $userAdmin->setNickname('admin');
        $manager->persist($userAdmin);

        // manager
        $userManager = new User();
        $userManager->setEmail('manager@gmail.com');
        $userManager->setRoles(['ROLE_MANAGER']);
        $userManager->setPassword('$2y$13$/U5OgXbXusW7abJveoqeyeTZZBDrq/Lzh8Gt1RXnEDbT2xJqbv3vi');
        $userManager->setNickname('manager');
        $manager->persist($userManager);

        // user
        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('$2y$13$ZqCHV23K0KMWmCxntdDlmOocuxuuSOXeT7nfKy2ZbE2vFC1VS3Q..');
        $user->setNickname('user');
        $manager->persist($user);

        // un tableau vide pour stocker nos categories
        // afin de les réutiliser pour chaque recette créé
        $categoryList = [];

        for ($i = 0; $i < 10; $i++) {
            // on crée une entité
            $category = new Category();
            // on la met à jour
            $category->setName($faker->unique()->recipeCategory());

            // on l'ajoute à sa liste
            $categoryList[] = $category;

            // on persist l'entité, avec l'entity manager fourni
            $manager->persist($category);
        }

        // un tableau vide pour stocker nos ingredients
        // afin de les réutiliser pour chaque recette créé
        $ingredientsList = [];

        for ($i = 0; $i < 100; $i++) {
            // on crée une entité
            $ingredients = new Ingredient();
            // on la met à jour
            $ingredients->setName($faker->unique()->recipeIngredients());

            // on l'ajoute à sa liste
            $ingredientsList[] = $ingredients;

            // on persist l'entité, avec l'entity manager fourni
            $manager->persist($ingredients);
        }


        // 50 recettes
        for ($r = 0; $r < 50; $r++) {

            $recipe = new Recipe();
            // on met à jour ses propriétés
            $recipe->setName($faker->unique()->recipeName());

            $personNumber = $faker->randomElement([2, 4]);
            $recipe->setPersonNumber($personNumber);

            $recipe->setDescription($faker->text(200));

            $status = $faker->randomElement([true, false]);
            $recipe->setStatus($status);

            // un tableau vide pour stocker nos steps
            // afin de les réutiliser pour chaque recette créé
            $stepsList = [];

            for ($i = 0; $i < 50; $i++) {
                // on crée une entité
                $steps = new Step();
                // on la met à jour
                $steps->setDescription($faker->text(mt_rand(50, 150)));

                // on l'ajoute à sa liste
                $stepsList[] = $steps;

                $steps->setRecipe($recipe);

                // on persist l'entité, avec l'entity manager fourni
                $manager->persist($steps);
            }

            // ici on va associer quelques steps à notre recette
            for ($s = 0; $s < mt_rand(5, 15); $s++) {
                // un step au hasard
                $nbSteps = count($stepsList);
                $randomStepIndex = mt_rand(0, $nbSteps - 1);
                // on associe l'élément tu tableau de steps à cet index
                $recipe->addStep($stepsList[$randomStepIndex]);
            }

            // ici on va associer quelques categories à notre recette
            for ($c = 0; $c < mt_rand(1, 3); $c++) {
                // une categorie au hasard
                $nbCat = count($categoryList);
                $randomCatIndex = mt_rand(0, $nbCat - 1);
                // on associe l'élément tu tableau de steps à cet index
                $recipe->addCategory($categoryList[$randomCatIndex]);
            }

            // ici on va associer quelques ingredients à notre recette
            for ($in = 0; $in < mt_rand(5, 20); $in++) {
                // un ingredient au hasard
                $nbIng = count($ingredientsList);
                $randomIngIndex = mt_rand(0, $nbIng - 1);
                // on associe l'élément tu tableau de steps à cet index
                $recipe->addIngredient($ingredientsList[$randomIngIndex]);
            }

            $recipe->setUser($user);

            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
