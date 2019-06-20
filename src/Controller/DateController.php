<?php
// src/Controller/DateController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/date")
 */
class DateController extends AbstractController
{

  /**
   * @Route("/", name="qtf_date_index")
   */
  public function index(Environment $twig)
  {
        return $this->render('Inside/Date/index.html.twig');
  }

  





}