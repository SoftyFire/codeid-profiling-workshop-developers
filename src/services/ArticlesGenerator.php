<?php

namespace app\services;

use app\models\Article;
use app\models\ArticleTags;
use app\models\Tag;
use BlackfireProbe;

/**
 * Class ArticlesGenerator
 *
 * Generates random news for testing purposes.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ArticlesGenerator
{
    /**
     * @var \joshtronic\LoremIpsum
     */
    private $ipsum;

    /**
     * ArticlesGenerator constructor.
     *
     * @param \joshtronic\LoremIpsum $ipsum
     */
    function __construct(\joshtronic\LoremIpsum $ipsum)
    {
        $this->ipsum = $ipsum;
    }

    /**
     * @param int $number Number of news to be generated
     * @return Article[]
     */
    public function generate($number): array
    {
        $articles = [];
        for ($i = 0; $i < $number; $i++) {
            BlackfireProbe::addMarker(__METHOD__);
            $articles[] = $this->createRandomArticles();
        }

        return $articles;
    }

    /**
     * @return Article
     */
    private function createRandomArticles(): Article
    {
        $article = new Article([
            'title' => $this->generateRandomTitle(),
            'text' => $this->generateRandomText(),
        ]);
        $article->save();

        $tags = $this->generateTagsForArticles($article);

        $rows = array_map(function (Tag $tag) use ($article) {
            return ['article_id' => $article->id, 'tag_id' => $tag->id];
        }, $tags);
        ArticleTags::getDb()->createCommand()->batchInsert(ArticleTags::tableName(), [
            'article_id', 'tag_id'
        ], $rows)->execute();

        return $article;
    }

    /**
     * @return Tag
     */
    private function getRandomTag()
    {
        $tags = [
            'hit',
            'politics',
            'culture',
            'technologies',
            'health',
            'music',
            'cinema',
            'climate',
            'science',
            'nature',
            'photography',
            'biology',
        ];

        $i = mt_rand(0, count($tags) - 1);

        return $this->ensureTag($tags[$i]);
    }

    /**
     * @var Tag[]
     */
    private $_tags = [];

    /**
     * @param string $name
     * @return Tag
     */
    private function ensureTag($name)
    {
        if (isset($this->_tags[$name])) {
            return $this->_tags[$name];
        }

        if ($tag = Tag::find()->where(['name' => $name])->one()) {
            $this->_tags[$name] = $tag;
            return $tag;
        }

        $tag = new Tag(['name' => $name]);
        $tag->save();

        $this->_tags[$name] = $tag;

        return $tag;
    }

    /**
     * @param Article $news
     * @return array
     */
    private function generateTagsForArticles($news): array
    {
        $count = mt_rand(1, 5);

        $tags = [];
        for ($i = 0; $i < $count; $i++) {
            $tags[] = $this->getRandomTag();
        }

        return $tags;
    }

    /**
     * @return string
     */
    private function generateRandomTitle()
    {
        return $this->ipsum->words(8);
    }

    /**
     * @return string
     */
    private function generateRandomText()
    {
        return $this->ipsum->paragraphs(2);
    }
}
