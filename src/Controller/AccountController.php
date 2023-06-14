<?php

namespace App\Controller;

use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]

class AccountController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/account', name: 'app_account')]
    public function index(ArticleService $articleService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $subscrDate = $user->getSubscriptionDate()->diff(new \DateTime())->days;
        $lastMonthArticles = $articleService->getLatestMonthPublished($user->getId());

        return $this->render('templates/account/index.html.twig', [
            'subscrDate' => $subscrDate,
            'lastMonthArticles' => count($lastMonthArticles)
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/subscription', name: 'app_subscription')]
    public function subscription(): Response
    {
        return $this->render('templates/homepage.html.twig');
    }
}
