<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginFormType;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    #[Route('/log-ind', name: 'login')] 
    public function auth(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) return $this->redirectToRoute('home');
        
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

    #[Route('/log-ud', name: 'logout')]
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('This should never be reached!');
    }

    
    #[Route('/register-bruger', name:'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer): Response
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

            // Set the user as inactive
            $user->setIsActive(false);

            // Set the user role
            $user->setRoles(['ROLE_User']);

            // Save the user
            $entityManager->persist($user);
            
            // Flush the buffer
            $entityManager->flush();
            
            // generate a signed url and email it to the user
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            
            // Render the Twig template and extract HTML content
            $htmlContent = $this->renderView('account/validate_email.html.twig', [
                'confirmation_link' => $signatureComponents->getSignedUrl()
            ]);
            
            $email = (new Email())
            ->from('oldefarsopskrifter@gmail.com')
            ->to($user->getEmail())
            ->subject('Please Confirm your Email')
            ->html($htmlContent);
            
            $mailer->send($email);
            
            // Redirect or render a success message
            return $this->redirectToRoute('confirm_email');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView(),
            'error' => false
        ]);
    }

    #[Route('/opdater-bruger-information', name:'edit')]
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

    #[Route('/validering', name: 'verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        // Match ID of the user
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('register');
        }
        
        // Match user with database based on ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if (null === $user) {
            return $this->redirectToRoute('register');
        }

        // Handle the verification of the user
        try {
            $verifyEmailHelper->validateEmailConfirmationFromRequest($request, $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('register');
        }

        $user->setIsActive(true);
        $entityManager->flush();

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }

    #[Route('/konfirmation_email', name: 'confirm_email')]
    public function confirmUserEmail() : Response
    {
        return $this->render('account/confirm_email.html.twig');
    }
}
