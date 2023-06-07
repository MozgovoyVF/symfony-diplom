<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Provider\ArticleContentProvider;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ArticleController extends AbstractController
{
  #[Route('/create_content', name: 'app_article_create_content')]
  public function createContent(Request $request, ArticleRepository $articleRepository , ArticleContentProvider $articleContentProvider, EntityManagerInterface $em, SessionInterface $session)
  {
    $error = '';
    $content = '';

    /** @var User $user */
    $user = $this->getUser();
    $limit = $articleRepository->findLatestHourPublished($user->getId());
    $disabled = false;

    if (count($limit) >= 2 and $user->getSubscription() !== 'pro') {
      $disabled = true;
    } 

    if (!$disabled && $request->isMethod('post')) {
      $data = $request->request->all();
      try {
        $data = $articleContentProvider->get($data, $_FILES);
        $content = $data['content'];

        /** @var Article $article */
        $article = (new Article())
          ->setTitle($data['title'])
          ->setDescription($data['description'])
          ->setContent($data['content'])
          ->setAuthor($user);

        $em->persist($article);
        $em->flush($article);

      } catch (Exception $e) {
        $error = $e->getMessage();
      }
    }
    $session->set('disabled', $disabled);
    $session->set('content', $content);
    $session->set('error', $error);

    return $this->redirectToRoute('app_article_create');
    
  }

  #[Route('/create', name: 'app_article_create')]
  public function create(ThemeRepository $themeRepository, SessionInterface $session)
  {
    $themes = $themeRepository->findAll();

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
