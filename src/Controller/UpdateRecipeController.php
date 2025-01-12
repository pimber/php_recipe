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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrationFormType;

class UpdateRecipeController extends AbstractController
{
    #[Route('/opdatere', name:'edit')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Says that user is the type of User or null
        /** @var User|null $user */
        $user = $this->getUser();
        $old_mail = $user->getEmail();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the user alreade exists
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['Email' => $user->getEmail()]);
            if ($existingUser && $user->getEmail() != $old_mail) {
                // Render an error message
                $user->setEmail($old_mail);
                return $this->render('account/edit.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'This email is already in use',
                ]);
            }
            
            // Encode the password
            if ($form->get('password')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            // Set the user role
            $user->setRoles(['ROLE_User']);

            // Save the user
            $entityManager->persist($user);
            
            // Flush the buffer
            $entityManager->flush();

            // Redirect or render a success message
            return $this->redirectToRoute('home');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
            'error' => false
        ]);
    }

    #[Route('/slet-opskrift/{id}', name:'delete')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $recipe = $entityManager->getRepository(Recipe::class)->find($id);
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('myrecipes');
    }
}