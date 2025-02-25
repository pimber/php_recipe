<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AboutController extends AbstractController
{
    #[Route('/om-oldefars' , name:'aboutGreatGrandfather')]
    public function aboutGreatGrandfather(): Response
    {
        return $this->render('pages/aboutGreatGrandfather.html.twig');
    }
}