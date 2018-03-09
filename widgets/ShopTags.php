<?php
namespace bl\cms\shop\widgets;

use yii\base\Widget;
use bl\cms\shop\common\entities\Category;

class ShopTags extends Widget {

    public $title;
    public $categories;

    public function init()
    {
        if (!isset($this->title)) {
            $this->title = \Yii::t('shop', 'Top Categories');
        }
        $this->categories = Category::find()
            ->where(['tag_cloud' => true, 'show' => true])
            ->orderBy(['position' => SORT_ASC])
            ->all();
    }

    public function run()
    {
        if(empty($this->categories)) {
            return '';
        }

        return $this->render('shop-tags', [
            'title' => $this->title,
            'categories' => $this->categories
        ]);
    }


}