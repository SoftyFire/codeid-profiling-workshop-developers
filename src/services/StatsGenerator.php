<?php

namespace app\services;

use app\models\Article;
use app\models\ArticlesStats;

/**
 * Class StatsGenerator
 *
 * Generates [[ArticlesStats]] out of existing news.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class StatsGenerator
{
    /**
     * @return ArticlesStats
     */
    public function generate(): ArticlesStats
    {
        return new ArticlesStats($this->wordsCount(), $this->newsCount());
    }

    /**
     * Counts words stats
     *
     * @return array
     */
    private function wordsCount(): array
    {
        /** @var Article $allNews */
        $allNews = Article::find()->select(['LOWER({{article}}.text) as text'])->asArray()->all();
        $wordsCount = [];

        foreach ($allNews as $news) {
            $this->countWordsInNews($news, $wordsCount);
        }

        return $wordsCount;
    }

    /**
     * @return int news count
     */
    private function newsCount(): int
    {
        return Article::find()->count();
    }

    /**
     * @param Article $news
     * @param $wordsCount
     * @return void
     */
    private function countWordsInNews($news, &$wordsCount): void
    {
        $text = preg_replace('/[^a-z0-9 ]/', ' ', $news['text']);
        $words = preg_split('/\s/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $wordsCount = array_count_values($words);
    }
}
