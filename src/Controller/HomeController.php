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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Recipe;
use App\Form\CreateNewRecipeType;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HomeController extends AbstractController
{
    #[Route('/', name:'home')]
    public function homepage(Request $request, EntityManagerInterface $entityManager, CacheInterface $cache): Response
    {
        // Calculate the time until midnight
        $now = new \DateTime();
        $midnight = new \DateTime('tomorrow midnight');
        $expiresAfter = $midnight->getTimestamp() - $now->getTimestamp();

        $cacheKey = 'homepage_recipes';

        $data = $cache->get($cacheKey, function(ItemInterface $item) use ($entityManager, $expiresAfter)
        {
            $item->expiresAfter($expiresAfter);
            // Get database connection
            $connection = $entityManager->getConnection();

            // Build the raw SQL query for PostgreSQL
            $sql = "SELECT r.id FROM recipe as r ORDER BY RANDOM() LIMIT 3";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();

            // Fetch results as an associative array
            return $result->fetchAllAssociative();
        });
        
        // Map results to Recipe entities
        $recipes = [];
        foreach ($data as $row) {
            $recipe = $entityManager->getRepository(Recipe::class)->find($row['id']);
            $recipes[] = $recipe;
        }

        $user = $this->getUser();

        $owner = false;

        return $this->render('pages/home.html.twig', [
            'user' => $user,
            'recipes' => $recipes,
            'owner' => $owner
        ]);
    }

    #[Route('/opskrifter', name: 'recipes')]
    public function recipes(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        // Searchterm for the recipes
        $searchTerm = $request->query->get('search');

        // Query builder for searching the recipes and user
        $queryBuilder = $entityManager->getRepository(Recipe::class)->createQueryBuilder('r')
            ->join('r.user_id', 'u')
            ->join('r.ingredients', 'a')
            ->join('a.ingredient_id', 'i');

        // Apply search filter if search term exists
        if ($searchTerm) {
            $queryBuilder
                ->where('r.name LIKE :searchTerm')
                ->orWhere('i.name LIKE :searchTerm')
                ->orWhere('u.Name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Get all recipes from DB
        $recipesQuery = $queryBuilder->getQuery();

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
            'owner' => $owner, // Pass the owner variable to the template
            'searchTerm' => $searchTerm // Pass the search term to the template
        ]);
    }

    #[Route('/opskrifter/{id}', name: 'recipe')]
    public function recipe($id, EntityManagerInterface $entityManager): Response
    {
        // Get the recipe from the database
        $recipe = $entityManager->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('The recipe does not exist');
        }

        $connection = $entityManager->getConnection();

        // Build the raw SQL query for PostgreSQL
        $sql = "SELECT r.id FROM recipe as r ORDER BY RANDOM() LIMIT 3";
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        // Fetch result as an associative array
        $rows = $result->fetchAllAssociative();

        // Map results to Recipe entities
        $recipes = [];
        foreach ($rows as $row) {
            $recipe = $entityManager->getRepository(Recipe::class)->find($row['id']);
            $recipes[] = $recipe;
        }


        // Set owner to false
        $owner = false;

        // Render the recipe page
        return $this->render('pages/new-showrecipe.html.twig', [
            'recipe' => $recipe,
            'owner' => $owner,
            'recipes' => $recipes
        ]);
    }

    #[Route('/kontakt', name:'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Create a form for the contact page
        $form = $this->createForm(ContactType::class);

        // Handle the form submission
        $form->handleRequest($request);

        // If the form is submitted and valid, send an email
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $emailMessage = (new Email())
                ->from('oldefarsopskrifter@outlook.dk')
                ->to('oldefarsopskrifter@outlook.dk')
                ->subject('Contact form submission')
                ->text("You have a new message from {$data['fullName']} ({$data['email']}):\n\n{$data['message']}");

            $mailer->send($emailMessage);

            // Send a success message to the user
            $this->addFlash('success', 'Your message has been sent successfully!');

            return $this->redirectToRoute('home');
        }

        return $this->render('pages/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mine-opskrifter', name:'myrecipes')]
    public function myrecipes(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, FormFactoryInterface $formFactory): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        // Search term for the myrecipes
        $searchTerm = $request->query->get('search');

        // Query builder for searching the myrecipes
        $queryBuilder = $entityManager->getRepository(Recipe::class)->createQueryBuilder('r')
            ->join('r.ingredients', 'a')
            ->join('a.ingredient_id', 'i')
            ->where('r.user_id = :user_id')
            ->setParameter('user_id', $user->getId());

        
        if ($searchTerm) {
            $queryBuilder
                ->andwhere('r.name LIKE :searchTerm')
                ->orWhere('i.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Get the query from the query builder
        $recipesQuery = $queryBuilder->getQuery();

        // Paginate the results
        $page = $request->query->getInt('page', 1);
        $recipes = $paginator->paginate(
            $recipesQuery,
            $page,
            6 // Number of items per page
        );

        $collection = [];

        foreach ($recipes as $recipe) {
            $recipeForm = $formFactory->createNamed($recipe->getId(), CreateNewRecipeType::class, $recipe);
            $ingredients = [];
            foreach ($recipe->getIngredients() as $ingredient) {
                $ingredients[] = [
                    'amount' => $ingredient->getAmount(),
                    'ingredient' => $ingredient->getIngredientId()->getName(),
                    'ingredientAmountType' => $ingredient->getIngredientAmountTypeId()->getType()
                ];
            }
            $recipeForm->get('ingredients')->setData($ingredients);
            $recipeForm->handleRequest($request);
            $this->handleFormRequest($recipeForm, $recipe, $user, $entityManager);
            $collection[] = [
                'id' => $recipe->getId(),
                'form' => $recipeForm,
                'formView' => $recipeForm->createView()
            ];
        }

        // Create Form handling
        $recipe = new Recipe();
        $form = $this->createForm(CreateNewRecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($this->handleFormRequest($form, $recipe, $user, $entityManager)) {
            return $this->redirectToRoute('myrecipes');
        }
        
        // Render the page
        return $this->render('pages/myrecipes.html.twig', [
            'form' => $form->createView(),
            'recipes' => $recipes,
            'searchTerm' => $searchTerm,
            'collection' => $collection
        ]);
        
    }

    private function handleFormRequest($form, Recipe $newRecipe, User $user, EntityManagerInterface $entityManager)
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            return;
        }
        $id = $form->getName();
        if ($id === 'create_new_recipe') {
            $recipe = $newRecipe;
            $new = true;
        } else {
            $recipe = $entityManager->getRepository(Recipe::class)->find($id);
            $new = false;
        }

        // Clear existing ingredients to avoid duplicates
        foreach ($recipe->getIngredients() as $existingIngredient) {
            $recipe->removeIngredient($existingIngredient);
            $entityManager->remove($existingIngredient);
        }

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
        if ($new) {
            $recipe->setCreatedOn(new \DateTime());
        }

        // Handle file upload
        /** @var UploadedFile $file */
        $file = $form->get('imageFile')->getData();

        if ($file) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/img/recipes/',
                $newFilename
            );

            $recipe->setImageName($newFilename);
        }

        $entityManager->persist($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('myrecipes');
    }

    #[Route('/slet/{id}', name:'delete')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $recipe = $entityManager->getRepository(Recipe::class)->find($id);
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('myrecipes');
    }

    #[Route('/om-oldefars' , name:'aboutGreatGrandfather')]
    public function aboutGreatGrandfather(): Response
    {
        return $this->render('pages/aboutGreatGrandfather.html.twig');
    }
}