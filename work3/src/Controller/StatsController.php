<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends AbstractController 
{
    public function index(): Response 
    {
        return $this->render('stats/index.html.twig');
    }
}