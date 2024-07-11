<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name:'home')]
    public function homepage(): Response
    {
        return $this->render('pages/homepage.html.twig');
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
    public function myrecipes(): Response
    {
        return $this->render('pages/myrecipes.html.twig');
    }
}