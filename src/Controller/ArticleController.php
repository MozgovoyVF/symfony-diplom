<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
  #[Route('/create', name: 'app_article_create')]
  public function create()
  {

    return $this->render(
      'article/article_create.html.twig',
      [

      ]
    );
  }
}
