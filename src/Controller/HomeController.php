<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\IngredientAmount;
use App\Entity\IngredientAmountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
use App\Form\CreateNewRecipeType;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/recipes', name:'recipes')]
    public function recipes(): Response
    {
        return $this->render('pages/recipes.html.twig');
    }

    #[Route('/contact', name:'contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }

    #[Route('/myrecipes', name:'myrecipes')]
    public function myrecipes(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $recipes = $user->getRecipes();

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