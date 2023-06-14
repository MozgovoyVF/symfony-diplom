<?php

namespace App\Controller;

use App\Service\ArticleContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        return $this->render('templates/homepage.html.twig');
    }

    /** 
     * @param Request $request
     * @param ArticleContentService $articleContent
     * @return Response
     */
    #[Route('/try', name: 'app_try')]
    public function try(Request $request, ArticleContentService $articleContent): Response
    {
        $params = $articleContent->createTrialContent($request->request->all());

        return $this->render(
            'tempaltes/try.html.twig',
            [
                'disabled' => $params['disabled'],
                'title' => $params['title'],
                'word' => $params['word'],
                'content' => $params['content']
            ]
        );
    }
}
