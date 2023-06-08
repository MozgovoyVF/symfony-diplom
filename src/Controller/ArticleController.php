<?php

namespace App\Controller;

use App\Service\ArticleContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\ThemeService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ArticleController extends AbstractController
{
  /**
   * Преобразует ввод, заданный в качестве первого аргумента.
   *
   * @param ArticleContentService $articleContentService
   * @param Request $request
   * @param SessionInterface $session
   */
  #[Route('/create_content', name: 'app_article_create_content', methods: ['POST'])]
  public function createContent(ArticleContentService $articleContentService, Request $request, SessionInterface $session): RedirectResponse
  {
    $params = $articleContentService->createContent($request);

    $session->set('disabled', $params['disabled']);
    $session->set('content', $params['content']);
    $session->set('error', $params['error']);

    return $this->redirectToRoute('app_article_create');
  }

  /**
   * Отображение страницы создания контента статьи.
   * @param ThemeService $themeService
   * @param SessionInterface $session
   */
  #[Route('/create', name: 'app_article_create')]
  public function create(ThemeService $themeService, SessionInterface $session): Response
  {
    $themes = $themeService->getAll();

    $disabled = $session->get('disabled', false);
    $content = $session->get('content', '');
    $error = $session->get('error', '');

    $session->set('disabled', false);
    $session->set('content', '');
    $session->set('error', '');

    return $this->render(
      'templates/article/article_create.html.twig',
      [
        'disabled' => $disabled,
        'content' => $content,
        'error' => $error,
        'themes' => $themes
      ]
    );
  }
}
