<?php

namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\form\CategoryImageForm;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\SearchCategory;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
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
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'roles' => ['viewShopCategoryList'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['save', 'add-basic', 'add-images', 'add-seo', 'delete-image',
                            'select-filters', 'delete-filter', 'up', 'down', 'switch-show'],
                        'roles' => ['saveShopCategory'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['delete'],
                        'roles' => ['deleteShopCategory'],
                        'allow' => true,
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all Category models.
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchCategory();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $languageId
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSave($id = null, $languageId = null)
    {

        if (!empty($id)) {
            $category = Category::findOne($id);
            $category_translation = CategoryTranslation::find()->where([
                'category_id' => $id,
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
                    return $this->redirect(Url::to(['/shop/category/save', 'id' => $category->id, 'languageId' => $category_translation->language_id]));
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

    /**
     * Deletes one category by id
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        Category::deleteAll(['id' => $id]);
        return $this->actionIndex();
    }

    /**
     * Basic category settings
     *
     * @param integer $languageId
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddBasic($id = null, $languageId = null)
    {
        if (!empty($id)) {
            $category = Category::findOne($id);
            $category_translation = CategoryTranslation::find()->where([
                'category_id' => $id,
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

    /**
     * Adds category images
     *
     * @param integer $languageId
     * @param integer $categoryId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddImages($categoryId, $languageId)
    {

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
        return $this->render('save', [
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

    /**
     * Adds category SEO data
     *
     * @param integer $languageId
     * @param integer $categoryId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddSeo($languageId = null, $categoryId = null)
    {
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
        return $this->render('save', [
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

    /**
     * Deletes one image from category
     *
     * @param integer $id
     * @param string $imageType
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDeleteImage($id, $imageType)
    {
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

    /**
     * Selects product filters for category
     *
     * @param integer $id
     * @param integer $languageId
     * @param integer $categoryId
     * @return mixed
     * @throws Exception
     * @throws ForbiddenHttpException
     */
    public function actionSelectFilters($id = null, $languageId = null, $categoryId = null)
    {

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

    /**
     * Deletes product filter from category
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDeleteFilter($id)
    {
        if (!empty($id)) {
            $filter = Filter::findOne($id);
            $filter->delete();

        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Changes category position to up
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUp($id)
    {
        if ($category = Category::findOne($id)) {
            $category->movePrev();
        }

        return $this->actionIndex();
    }

    /**
     * Changes category position to down
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDown($id)
    {
        if ($category = Category::findOne($id)) {
            $category->moveNext();
        }

        return $this->actionIndex();
    }

    /**
     * Shows or hides category
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSwitchShow($id)
    {
        $category = Category::findOne($id);
        if (!empty($category)) {
            $category->show = !$category->show;
            $category->save();
        }
        return $this->actionIndex();
    }

}