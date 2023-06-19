<?php

namespace App\Service;

use App\Repository\ArticleRepository;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    private ArticleRepository $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
    /**
     * @param int $id
     * @return array
     */
    public function getLatestMonthPublished(int $id): array
    {
        return $this->articleRepository->findLatestMonthPublished($id);
    }
}
