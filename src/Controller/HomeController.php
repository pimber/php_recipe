<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\User;
use App\Entity\IngredientAmount;
use App\Entity\IngredientAmountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
use App\Form\CreateNewRecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name:'home')]
    public function homepage(): Response
    {
        $user = $this->getUser();
        return $this->render('pages/homepage.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/recipes', name: 'recipes')]
    public function recipes(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        // Get all recipes from DB
        $recipesQuery = $entityManager->getRepository(Recipe::class)->findAll();

        // Paginate the results
        $page = $request->query->getInt('page', 1);
        $recipes = $paginator->paginate(
            $recipesQuery,
            $page,
            6 // Number of items per page
        );
        
        // Set owner to false
        $owner = false;

        // Render the page
        return $this->render('pages/recipes.html.twig', [
            'recipes' => $recipes,
            'owner' => $owner // Pass the owner variable to the template
        ]);
    }

    #[Route('/contact', name:'contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }

    #[Route('/myrecipes', name:'myrecipes')]
    public function myrecipes(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        // Get all the recipes the user has created
        $recipesQuery = $user->getRecipes();

        // Paginate the results
        $page = $request->query->getInt('page', 1);
        $recipes = $paginator->paginate(
            $recipesQuery,
            $page,
            6 // Number of items per page
        );

        // Form handling
        $recipe = new Recipe();
        $form = $this->createForm(CreateNewRecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Iterate ingredients in the form
            $ingredientsData = $form->get('ingredients')->getData();
            foreach ($ingredientsData as $ingredientData) {
                $ingredient = new IngredientAmount();
                // Set amount
                $ingredient->setAmount($ingredientData['amount']);

                // Handle ingredient name
                $name = $ingredientData['ingredient'];
                $existingName = $entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $name]);
                if ($existingName) {
                    $ingredient->setIngredientId($existingName);
                } else {
                    $newName = new Ingredient();
                    $newName->setName($name);
                    $entityManager->persist($newName);
                    $ingredient->setIngredientId($newName);
                }

                // Handle amount type
                $type = $ingredientData['ingredientAmountType'];
                $existingType = $entityManager->getRepository(IngredientAmountType::class)->findOneBy(['type' => $type]);
                if ($existingType) {
                    $ingredient->setIngredientAmountTypeId($existingType);
                } else {
                    $newType = new IngredientAmountType();
                    $newType->setType($type);
                    $entityManager->persist($newType);
                    $ingredient->setIngredientAmountTypeId($newType);
                }

                // Save ingredient
                $entityManager->persist($ingredient);
                $recipe->addIngredient($ingredient);
            }

            // Set user and created on for the recipe
            $recipe->setUserId($user);
            $recipe->setCreatedOn(new \DateTime());

            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('myrecipes');
        }

        return $this->render('pages/myrecipes.html.twig', [
            'form' => $form->createView(),
            'recipes' => $recipes,
        ]);
        
    }
}