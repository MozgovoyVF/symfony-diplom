<?php

namespace App\Service;

use App\Repository\ArticleRepository;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
    /**
     * @param int $id
     * @return array
     */
    public function getLatestMonthPublished($id): array
    {
        return $this->articleRepository->findLatestMonthPublished($id);
    }
}
