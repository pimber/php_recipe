<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/edit', name:'edit')]
    public function edit(): Response
    {
        return $this->render('account/edit.html.twig');
    }

    #[Route('/login', name:'login')]
    public function login(): Response
    {
        return $this->render('account/login.html.twig');
    }

    #[Route('/register', name:'register')]
    public function register(): Response
    {
        return $this->render('account/register.html.twig');
    }
}