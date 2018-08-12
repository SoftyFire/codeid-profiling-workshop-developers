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
        $texts = Article::find()->select('lower(text) as text')->asArray()->all();

        return new ArticlesStats($this->wordsCount($texts), $this->newsCount($texts));
    }

    /**
     * Counts words stats
     *
     * @param array $texts
     * @return array
     */
    private function wordsCount(array $texts): array
    {
        $bigText = implode(' ', array_column($texts, 'text'));
        return $this->countWordsInNews($bigText);
    }

    /**
     * @param array $texts
     * @return int news count
     */
    private function newsCount(array $texts): int
    {
        return \count($texts);
    }

    /**
     * @param string $input
     * @return array
     */
    private function countWordsInNews(string $input): array
    {
        $text = preg_replace('/[^a-z0-9 ]/', ' ', $input);
        $words = preg_split('/\s/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return array_count_values($words);
    }
}
