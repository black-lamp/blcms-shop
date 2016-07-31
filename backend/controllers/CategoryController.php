<?php

namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\backend\components\form\CategoryImageForm;
use Yii;
use yii\web\Controller;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov
 */

class CategoryController extends Controller
{
    public function actionIndex() {
        return $this->render('index', [
            'categories' => Category::find()
                ->with(['translations'])
                ->orderBy(['parent_id' => SORT_ASC, 'position' => SORT_ASC])
                ->all(),
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
            if(empty($category_translation)) $category_translation = new CategoryTranslation();
        }
        else {
            $category = new Category();
            $category_translation = new CategoryTranslation();
        }

        $image_form = new CategoryImageForm();

        if(Yii::$app->request->isPost) {

            $category->load(Yii::$app->request->post());
            $category_translation->load(Yii::$app->request->post());

            $image_form->cover = UploadedFile::getInstance($image_form, 'cover');
            $image_form->thumbnail = UploadedFile::getInstance($image_form, 'thumbnail');
            $image_form->menu_item = UploadedFile::getInstance($image_form, 'menu_item');

            if (!empty($image_form->cover) || !empty($image_form->thumbnail) || !empty($image_form->menu_item)) {
                $image_name = $image_form->upload();
                if (!empty($image_form->cover)) {
                    $category->cover = $image_name['cover'];
                }
                if (!empty($image_form->thumbnail)) {
                    $category->thumbnail = $image_name['thumbnail'];
                }
                if (!empty($image_form->menu_item)) {
                    $category->menu_item = $image_name['menu_item'];
                }
            }

            if($category->validate() && $category_translation->validate())
            {
                $category->save();
                $category_translation->category_id = $category->id;
                $category_translation->language_id = $languageId;
                $category_translation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
            }
        }

        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

        return $this->render('save', [
            'category' => $category,
            'category_translation' => $category_translation,
            'categories' => Category::find()->with('translations')->all(),
            'selectedLanguage' => Language::findOne($languageId),
            'languages' => Language::findAll(['active' => true]),
            'image_form' => $image_form,
            'maxPosition' => Category::find()->orderBy(['position' => SORT_DESC])->one()->position,
            'minPosition' => Category::find()->orderBy(['position' => SORT_ASC])->one()->position,
            'categoriesWithoutParent' => $categoriesWithoutParent,
            'categoriesTree' => Category::findChilds($categoriesWithoutParent),
        ]);
    }

    public function actionDelete($id) {
        Category::deleteAll(['id' => $id]);
        return $this->actionIndex();
    }

    public function actionDeleteImage($id, $type) {
        $dir = Yii::getAlias('@frontend/web/images');

        if (!empty($id) && !empty($type)) {
            $category = Category::findOne($id);

            unlink($dir . '/shop-category/' . $type . '/' . $category->$type . '-big.jpg');
            unlink($dir . '/shop-category/' . $type . '/' . $category->$type . '-small.jpg');
            unlink($dir . '/shop-category/' . $type . '/' . $category->$type . '-thumb.jpg');
            $category->$type = null;
            $category->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUp($id) {
        if($category = Category::findOne($id)) {
            $category->movePrev();
        }

        return $this->actionIndex();
    }

    public function actionDown($id) {
        if($category = Category::findOne($id)) {
            $category->moveNext();
        }

        return $this->actionIndex();
    }

    public function actionSwitchShow($id) {
        if($category = Category::findOne($id)) {
            $category->show = !$category->show;
            $category->save();
        }
        return $this->actionIndex();
    }
}