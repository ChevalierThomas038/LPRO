<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SpamFinder;
use App\Service\LoggerSpam;

class ServicesController extends AbstractController
{
    #[Route('/testSpam/{text}')]
    public function testLogger(string $text): Response
    {
        $text->send($text);
        return new Response('<body></body>');
    }
}
