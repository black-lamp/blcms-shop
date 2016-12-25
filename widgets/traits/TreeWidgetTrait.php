<?php
namespace bl\cms\shop\widgets\traits;

use bl\multilang\entities\Language;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use bl\cms\shop\common\entities\Category;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
trait TreeWidgetTrait
{
    /**
     * @param null|integer $parentId
     * @param integer $level
     * @param integer $currentCategoryId
     * @param boolean $isGrid
     * @param string $downIconClass
     * @param string $upIconClass
     * @return mixed
     * @throws BadRequestHttpException
     * @throws Exception
     *
     * This action is used by Tree wiget
     */
    public function actionGetCategories($parentId = null, $level, $currentCategoryId, $isGrid = false, $downIconClass, $upIconClass)
    {
        if (\Yii::$app->request->isAjax) {

            if (!empty($level)) {
                $categories = Category::find()->where(['parent_id' => $parentId])->orderBy('position')->all();

                $params = [
                    'categories' => $categories,
                    'level' => $level,
                    'currentCategoryId' => $currentCategoryId,
                    'languageId' => Language::getCurrent()->id,
                    'downIconClass' => $downIconClass,
                    'upIconClass' => $upIconClass
                ];
                /**
                 * @var $this \yii\web\Controller
                 */
                if ($isGrid) {
                    return $this->renderAjax(
                        '@vendor/black-lamp/blcms-shop/widgets/views/tree/grid-tr', $params);
                }
                else {
                    return $this->renderAjax(
                        '@vendor/black-lamp/blcms-shop/widgets/views/tree/categories-ajax', $params);
                }
            } else throw new Exception();
        } else throw new BadRequestHttpException();
    }
}
