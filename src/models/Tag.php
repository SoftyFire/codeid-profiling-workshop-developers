<?php


namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Tag
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @property int $id
 * @property string $name
 * @property Article[] $news
 */
class Tag extends ActiveRecord
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    public function getNews()
    {
        return $this->hasMany(Article::class, ['id' => 'article_id'])->viaTable('article_tags', ['article_id' => 'id'])   ;
    }

//    public function getTagNews()
//    {
//        return $this->hasMany(ArticleTags::class, ['tag_id' => 'id']);
//    }
}
