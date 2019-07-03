<?php
// src/Controller/WelcomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @Route("/out/welcome")
 */
class WelcomeController extends AbstractController
{

    /**
     * @Route("/", name="qtf_welcome_index")
     */
    public function index(Request $request)
    {
        return $this->render('Outside/index.html.twig');
    }


}
