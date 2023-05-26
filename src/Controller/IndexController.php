<?php

namespace App\Controller;

use App\Provider\ArticleContentProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_homepage')]
  public function homepage()
  {

    return $this->render(
      'homepage.html.twig',
      [

      ]
    );
  }

  #[Route('/try', name: 'app_try')]
  public function try(Request $request, ArticleContentProvider $articleProvider)
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

    $content = $articleProvider->get($text, $word);

    return $this->render(
      'try.html.twig',
      [
        'disabled' => $disabled,
        'title' => $title,
        'word' => $word,
        'content' => $content
      ]
    );
  }
}
