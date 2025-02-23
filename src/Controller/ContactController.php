<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ContactType;


class ContactController extends AbstractController
{
    #[Route('/kontakt', name:'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Create a form for the contact page
        $form = $this->createForm(ContactType::class);

        // Handle the form submission
        $form->handleRequest($request);

        // If the form is submitted and valid, send an email
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $emailMessage = (new Email())
                ->from('oldefarsopskrifter@outlook.dk')
                ->to('oldefarsopskrifter@outlook.dk')
                ->subject('Contact form submission')
                ->text("You have a new message from {$data['fullName']} ({$data['email']}):\n\n{$data['message']}");

            $mailer->send($emailMessage);

            // Send a success message to the user
            $this->addFlash('success', 'Your message has been sent successfully!');

            return $this->redirectToRoute('home');
        }

        return $this->render('pages/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}