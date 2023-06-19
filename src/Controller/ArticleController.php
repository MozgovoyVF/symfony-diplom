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
     * @param ArticleContentService $articleContentService
     * @param Request $request
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    #[Route('/create_content', name: 'app_article_create_content', methods: ['POST'])]
    public function createContent(ArticleContentService $articleContentService, Request $request, SessionInterface $session): RedirectResponse
    {
        $params = $articleContentService->createContent($request->request->all());

        $session->set('create_disabled', $params['disabled']);
        $session->set('article_content', $params['content']);
        $session->set('content_error', $params['error']);

        return $this->redirectToRoute('app_article_create');
    }

    /**
     * @param ThemeService $themeService
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/create', name: 'app_article_create')]
    public function create(ThemeService $themeService, SessionInterface $session): Response
    {
        $themes = $themeService->getLastThemes();

        $disabled = $session->get('create_disabled', false);
        $content = $session->get('article_content', '');
        $error = $session->get('content_error', '');

        $session->set('create_disabled', false);
        $session->set('article_content', '');
        $session->set('content_error', '');

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
