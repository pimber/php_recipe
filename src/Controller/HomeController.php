<?php

namespace App\Controller;

require "import.php";

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
}