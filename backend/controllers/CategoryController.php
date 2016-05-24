<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 20.05.2016
 * Time: 10:57
 */
namespace bl\cms\shop\backend\controllers;

use Yii;
use yii\web\Controller;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\Url;

class CategoryController extends Controller
{
    public function actionIndex() {
        return $this->render('index', [
            'categories' => Category::find()->with(['translations'])->all(),
            'languages' => Language::findAll(['active' => true])
        ]);
    }
    public function actionSave($languageId = null, $categoryId = null) {
        if (!empty($categoryId)) {
            $category = Category::findOne($categoryId);
            $category_translation = CategoryTranslation::find()->where([
                'category_id' => $categoryId,
                'language_id' => $languageId
            ])->one();
            if(empty($category_translation))
                $category_translation = new CategoryTranslation();
        } else {
            $category = new Category();
            $category_translation = new CategoryTranslation();
        }
        if(Yii::$app->request->isPost) {
            $category->load(Yii::$app->request->post());
            $category_translation->load(Yii::$app->request->post());
            if($category->validate() && $category_translation->validate())
            {
                $category->save();
                $category_translation->category_id = $category->id;
                $category_translation->language_id = $languageId;
                $category_translation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->redirect(Url::toRoute('/multishop/category'));
            }
            else
                Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
        }
        return $this->render('save', [
            'item' => $category,
            'category_translation' => $category_translation,
            'category' => Category::find()->with('translations')->all(),
            'selectedLanguage' => Language::findOne($languageId),
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionDelete($id) {
        Category::deleteAll(['id' => $id]);
        return $this->redirect(Url::to(['/multishop/category']));
    }


}