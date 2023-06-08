<?php

namespace App\Service;

use App\Entity\Article;
use App\Provider\ArticleContentProvider;
use App\Provider\ArticleContentTryProvider;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;


class ArticleContentService
{
  /**
   * @var ArticleRepository
   */
  private $articleRepository;
  /**
   * @var ArticleContentProvider
   */
  private $articleContentProvider;
  /**
   * @var EntityManagerInterface
   */
  private $em;
  /**
   * @var Security
   */
  private $security;
  /**
   * @var ArticleContentTryProvider
   */
  private $articleProvider;
  private $error = '';
  private $content = '';

  public function __construct(
    ArticleRepository $articleRepository,
    ArticleContentProvider $articleContentProvider,
    EntityManagerInterface $em,
    Security $security,
    ArticleContentTryProvider $articleProvider
  ) {
    $this->articleRepository = $articleRepository;
    $this->articleContentProvider = $articleContentProvider;
    $this->em = $em;
    $this->security = $security;
    $this->articleProvider = $articleProvider;
  }

  public function createContent(Request $request): array
  {

    /** @var User $user */
    $user = $this->security->getUser();
    $limit = $this->articleRepository->findLatestHourPublished($user->getId());
    $disabled = false;

    if (count($limit) >= 2 and $user->getSubscription() !== 'pro') {
      $disabled = true;
    }

    if (!$disabled && $request->isMethod('post')) {
      $data = $request->request->all();
      try {
        $data = $this->articleContentProvider->get($data, $_FILES);
        $this->content = $data['content'];

        /** @var Article $article */
        $article = (new Article())
          ->setTitle($data['title'])
          ->setDescription($data['description'])
          ->setContent($data['content'])
          ->setAuthor($user);

        $this->em->persist($article);
        $this->em->flush($article);
      } catch (Exception $e) {
        $this->error = $e->getMessage();
      }
    }

    return
      [
        'disabled' => $disabled,
        'content' => $this->content,
        'error' => $this->error,
      ];
  }

  public function createTrialContent(Request $request): array
  {

    $session = $request->getSession();
    $disabled = false;
    $title = '';
    $word = '';
    $text = <<<EOF
    Сезонность работ является главной особенностью бизнеса по бурению скважин. Почти на все территории России зима — время заморозков. 
    Земля в этот период не подходит для бурения скважины. Основные работы начинают в марте и заканчивают в ноябре, и только в самых теплых регионах страны. 
    
    На Севере время начала и окончания работ сдвигается. Однако современные технологии иногда позволяют бурение и в зимний период.

    Скважину можно пробурить на разную глубину, в зависимости от места бурения и качества добываемой воды. Всё это необходимо определить с помощью 
    экспертиз до начала работ.

    Сейчас практически в каждом регионе России есть тенденция к расширению строительства частных домов — за исключением некоторых областей.
    Прежде чем организовать бизнес на бурении скважин на воду, проанализируйте спрос на услугу в вашем регионе. 
    Не ограничивайтесь рамками одного города, так как этот бизнес мобильный.
    EOF;

    if ($session->get('user_title') && $session->get('user_word')) {
      $disabled = true;
      $title = $session->get('user_title');
      $word = $session->get('user_word');
    } else {
      if ($request->query->all()) {
        $title = $request->query->get('title');
        $word = $request->query->get('word');

        $session->set('user_title', $title);
        $session->set('user_word', $word);
      }
    }

    $content = $this->articleProvider->get($text, $word);

    return [
      'disabled' => $disabled,
      'title' => $title,
      'word' => $word,
      'content' => $content
    ];
  }
}
