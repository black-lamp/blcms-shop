<?php
namespace bl\cms\shop\widgets;

use bl\cms\shop\widgets\assets\InputTreeAsset;
use bl\multilang\entities\Language;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class InputTree extends Widget
{
    /**
     * @var ActiveRecord
     */
    public $className;

    public $form;
    public $model;
    public $attribute;

    public $languageId = 1;

    public function init()
    {
        InputTreeAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        $parents = self::findChildren($this->className, null);

        $languages = Language::find()->all();
        foreach ($languages as $key => $language) {
            if ($language->id == $this->languageId) {
                $this->languageId = $key;
            }
        }

        return $this->render('input-tree/index',
            [
                'parents' => $parents,
                'form' => $this->form,
                'model' => $this->model,
                'attribute' => $this->attribute,
                'languageId' => $this->languageId
            ]);

    }

    /**
     * @param $parentId
     * @param $model ActiveRecord
     *
     * @return array
     */
    public static function findChildren($model, $parentId) {
        return $children = $model::find()->where(['parent_id' => $parentId])->all();
    }

}