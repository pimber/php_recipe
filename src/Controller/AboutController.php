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

class AboutController extends AbstractController
{
    #[Route('/om-oldefars' , name:'aboutGreatGrandfather')]
    public function aboutGreatGrandfather(): Response
    {
        return $this->render('pages/aboutGreatGrandfather.html.twig');
    }
}