<?php

namespace App\Controller;

require "import.php";

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

    #[Route('/slet/{id}', name:'delete')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $recipe = $entityManager->getRepository(Recipe::class)->find($id);
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('myrecipes');
    }
}