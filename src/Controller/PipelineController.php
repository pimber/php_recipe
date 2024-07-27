<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PipelineController extends AbstractController
{
    
    #[Route("/pipeline", name:"pipeline", methods: ["POST"])]
    public function index(Request $request): Response
    {
        $webhookSecret = $_ENV['WEBHOOK_SECRET'] ?? '';

        if (empty($webhookSecret)) {
            return new Response("Webhook secret not configured\n", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Read the raw POST data
        $postInput = $request->getContent();
        $ref = json_decode($postInput, true);

        // Get headers and calculate hash
        $postHash = strtolower($request->headers->get('x-hub-signature-256'));
        $hash = 'sha256=' . hash_hmac('sha256', $postInput, $webhookSecret);

        // Wrong hash then close request
        if ($hash !== $postHash) {
            return new Response("Mismatch of hashes\n", Response::HTTP_FORBIDDEN);
        }

        // Check if the post which branch it comes from
        $dir = '';
        if ($ref['ref'] === 'refs/heads/main') {
            $dir = 'php_recipe';
            $message = 'Deploying to main branch';
        } elseif ($ref['ref'] === 'refs/heads/dev') {
            $dir = 'dev_php_recipe';
            $message = 'Deploying to dev branch';
        } else {
            return new Response("Branch not supported\n", Response::HTTP_OK);
        }

        // Execute git pull command
        if ($dir !== '') {
            $output = shell_exec("cd /var/www/html/$dir && git pull");
            $message .= "\n" . $output;
        }

        // Return the message
        return new Response($message . "\n", Response::HTTP_OK);
    }
}