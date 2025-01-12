<?php

namespace App\Controller;

require "import.php";

class AboutController extends AbstractController
{
    #[Route('/om-oldefars' , name:'aboutGreatGrandfather')]
    public function aboutGreatGrandfather(): Response
    {
        return $this->render('pages/aboutGreatGrandfather.html.twig');
    }
}