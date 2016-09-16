<?php

namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\backend\components\form\CategoryImageForm;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\SearchCategory;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 * @author Albert Gainutdinov
 */

class CategoryController extends Controller
{

    /**
     * Lists all Category models.
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            $searchModel = new SearchCategory();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else throw new ForbiddenHttpException();
    }


    public function actionSave($languageId = null, $categoryId = null) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($categoryId)) {
                $category = Category::findOne($categoryId);
                $category_translation = CategoryTranslation::find()->where([
                    'category_id' => $categoryId,
                    'language_id' => $languageId
                ])->one();
                if (empty($category_translation)) $category_translation = new CategoryTranslation();
            } else {
                $category = new Category();
                $category_translation = new CategoryTranslation();
            }

            if (Yii::$app->request->isPost) {

                $category->load(Yii::$app->request->post());
                $category_translation->load(Yii::$app->request->post());

                if ($category->validate()) {

                    $category->save();
                    $category_translation->category_id = $category->id;
                    $category_translation->language_id = $languageId;
                    if ($category_translation->validate()) {
                        $category_translation->save();
                        Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                    }

                }
            }

            $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

            $maxPositionCategory = Category::find()->where(['parent_id' => $category->parent_id])->orderBy(['position' => SORT_DESC])->one();
            $maxPosition = (!empty($maxPositionCategory)) ? $maxPositionCategory->position : 0;
            $minPositionCategory = Category::find()->where(['parent_id' => $category->parent_id])->orderBy(['position' => SORT_ASC])->one();
            $minPosition = (!empty($minPositionCategory)) ? $minPositionCategory->position : 0;

            return $this->render('save', [
                'viewName' => 'add-basic',
                'selectedLanguage' => Language::findOne($languageId),
                'category' => $category,
                'languages' => Language::findAll(['active' => true]),

                'params' => [
                    'maxPosition' => $maxPosition,
                    'minPosition' => $minPosition,
                    'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                    'category' => $category,
                    'category_translation' => $category_translation,
                    'categories' => Category::find()->with('translations')->all(),
                    'selectedLanguage' => Language::findOne($languageId),
                ]
            ]);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionDelete($id) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            Category::deleteAll(['id' => $id]);
            return $this->actionIndex();
        }
        else throw new ForbiddenHttpException();
    }

    public function actionAddBasic($languageId = null, $categoryId = null) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($categoryId)) {
                $category = Category::findOne($categoryId);
                $category_translation = CategoryTranslation::find()->where([
                    'category_id' => $categoryId,
                    'language_id' => $languageId
                ])->one();
                if (empty($category_translation)) $category_translation = new CategoryTranslation();
            } else {
                $category = new Category();
                $category_translation = new CategoryTranslation();
            }

            if (Yii::$app->request->isPost) {

                $category->load(Yii::$app->request->post());
                $category_translation->load(Yii::$app->request->post());

                if ($category->validate() && $category_translation->validate()) {
                    $category->save();
                    $category_translation->category_id = $category->id;
                    $category_translation->language_id = $languageId;
                    $category_translation->save();
                    Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                }
            }

            $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();
            if (\Yii::$app->request->isPjax) {
                return $this->renderPartial('add-basic', [
                    'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                    'category' => $category,
                    'category_translation' => $category_translation,
                    'languageId' => $languageId,
                    'selectedLanguage' => Language::findOne($languageId),
                    'categoriesWithoutParent' => $categoriesWithoutParent
                ]);
            } else return $this->render('save', [
                'category' => $category,
                'languageId' => $languageId,
                'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                'selectedLanguage' => Language::findOne($languageId),
                'languages' => Language::findAll(['active' => true]),
                'viewName' => 'add-basic',
                'params' => [
                    'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                    'category' => $category,
                    'category_translation' => $category_translation,
                    'languageId' => $languageId,
                    'categoriesWithoutParent' => $categoriesWithoutParent
                ]
            ]);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionAddImages($categoryId, $languageId) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($categoryId)) {
                $category = Category::findOne($categoryId);
            } else {
                $category = new Category();
            }

            $image_form = new CategoryImageForm();

            if (Yii::$app->request->isPost) {

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
                if ($category->validate()) {
                    $category->save();
                }
            }
            if (\Yii::$app->request->isPjax) {
                return $this->renderPartial('add-images', [
                    'category' => $category,
                    'image_form' => $image_form,
                    'languageId' => $languageId
                ]);
            } else return $this->render('save', [
                'category' => $category,
                'languageId' => $languageId,
                'selectedLanguage' => Language::findOne($languageId),
                'languages' => Language::findAll(['active' => true]),
                'viewName' => 'add-images',
                'params' => [
                    'category' => $category,
                    'image_form' => $image_form,
                    'languageId' => $languageId
                ]
            ]);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionAddSeo($languageId = null, $categoryId = null) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($categoryId)) {
                $category = Category::findOne($categoryId);
                $category_translation = CategoryTranslation::find()->where([
                    'category_id' => $categoryId,
                    'language_id' => $languageId
                ])->one();
                if (empty($category_translation)) $category_translation = new CategoryTranslation();
            } else {
                $category = new Category();
                $category_translation = new CategoryTranslation();
            }

            if (Yii::$app->request->isPost) {

                $category->load(Yii::$app->request->post());
                $category_translation->load(Yii::$app->request->post());

                if ($category->validate() && $category_translation->validate()) {
                    $category->save();
                    $category_translation->category_id = $category->id;
                    $category_translation->language_id = $languageId;
                    $category_translation->save();
                    Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                }
            }
            if (\Yii::$app->request->isPjax) {
                return $this->renderPartial('add-seo', [
                    'category' => $category,
                    'category_translation' => $category_translation,
                    'languageId' => $languageId
                ]);
            } else return $this->render('save', [
                'category' => $category,
                'languageId' => $languageId,
                'selectedLanguage' => Language::findOne($languageId),
                'languages' => Language::findAll(['active' => true]),
                'viewName' => 'add-seo',
                'params' => [
                    'category' => $category,
                    'category_translation' => $category_translation,
                    'languageId' => $languageId
                ]
            ]);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionDeleteImage($id, $imageType, $languageId) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($id) && !empty($imageType)) {
                $category = Category::findOne($id);

                unlink(Category::getBig($category, $imageType));
                unlink(Category::getSmall($category, $imageType));
                unlink(Category::getThumb($category, $imageType));

                $category->$imageType = '';
                $category->save();
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        else throw new ForbiddenHttpException();
    }


    public function actionSelectFilters($id = null, $languageId = null, $categoryId = null) {

        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($categoryId)) {
                $category = Category::findOne($categoryId);
                $filters = Filter::find()->where(['category_id' => $category->id])->all();

                $filter = (!empty($id)) ? Filter::findOne($id) : new Filter();
            } else throw new Exception('You can not add filter before saving category.');

            if (Yii::$app->request->isPost) {

                $filter->load(Yii::$app->request->post());

                if ($filter->validate()) {
                    $filter->category_id = $category->id;
                    $filter->save();

                    Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }

            return $this->render('save', [
                'category' => $category,
                'languageId' => $languageId,
                'selectedLanguage' => Language::findOne($languageId),
                'languages' => Language::findAll(['active' => true]),
                'viewName' => 'select-filters',
                'params' => [
                    'category' => $category,
                    'filters' => $filters,
                    'newFilter' => new Filter(),
                    'languageId' => $languageId
                ]
            ]);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionDeleteFilter($id) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if (!empty($id)) {
                $filter = Filter::findOne($id);
                $filter->delete();

            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        else throw new ForbiddenHttpException();
    }

    public function actionUp($id) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if ($category = Category::findOne($id)) {
                $category->movePrev();
            }

            return $this->actionIndex();
        }
        else throw new ForbiddenHttpException();
    }

    public function actionDown($id) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            if ($category = Category::findOne($id)) {
                $category->moveNext();
            }

            return $this->actionIndex();
        }
        else throw new ForbiddenHttpException();
    }

    public function actionSwitchShow($id) {
        if (\Yii::$app->user->can('viewCompleteProductList')) {

            $category = Category::findOne($id);
            if (!empty($category)) {
                $category->show = !$category->show;
                $category->save();
            }
            return $this->actionIndex();
        }
        else throw new ForbiddenHttpException();
    }

}