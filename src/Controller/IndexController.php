<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(
        '/{spaRouting}',
        name: 'index',
        requirements: ['spaRouting' => '.*'],
        defaults: ['spaRouting' => null],
        methods: ['GET'],
        priority: -2
    )]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
