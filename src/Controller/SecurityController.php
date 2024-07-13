<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function auth(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class, [
            'last_email' => $lastUsername,
        ]);

        return $this->render('account/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('This should never be reached!');
    }

    
    #[Route('/register', name:'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the user alreade exists
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['Email' => $user->getEmail()]);
            if ($existingUser) {
                // Render an error message
                return $this->render('account/register.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'This email is already in use',
                ]);
            }
            
            // Encode the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Set the user role
            $user->setRoles(['User']);

            // Save the user
            $entityManager->persist($user);
            
            // Flush the buffer
            $entityManager->flush();

            // Redirect or render a success message
            return $this->redirectToRoute('home');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView(),
            'error' => false
        ]);
    }

    #[Route('/edit', name:'edit')]
    public function edit(): Response
    {
        return $this->render('account/edit.html.twig');
    }
}
