<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]

class AccountController extends AbstractController
{
  /**
   * Реализует отображение личного кабинета пользователя.
   */
  #[Route('/account', name: 'app_account')]
  public function index(ArticleRepository $articleRepository): Response
  {
    /** @var User $user */
    $user = $this->getUser();
    $subscrDate = $user->getSubscriptionDate()->diff(new \DateTime())->days;
    $lastMonthArticles = $articleRepository->findLatestMonthPublished($user->getId());

    return $this->render('templates/account/index.html.twig', [
      'subscrDate' => $subscrDate,
      'lastMonthArticles' => count($lastMonthArticles)
    ]);
  }

  /**
     * Реализует отображение страницы подписок пользователя.
     */
  #[Route('/subscription', name: 'app_subscription')]
  public function subscription(): Response
  {
    return $this->render('templates/homepage.html.twig');
  }
}
