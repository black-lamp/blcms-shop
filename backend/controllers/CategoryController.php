<?php
namespace bl\cms\shop\backend\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\AccessControl;
use bl\multilang\entities\Language;
use bl\cms\shop\backend\components\events\CategoryEvent;
use bl\cms\shop\backend\components\form\CategoryImageForm;
use yii\web\{ForbiddenHttpException, NotFoundHttpException, UploadedFile};
use bl\cms\shop\common\entities\{Category, CategoryTranslation, Filter, SearchCategory};

/**
 * CategoryController implements the CRUD actions for Category model.
 * @author Albert Gainutdinov
 */
class CategoryController extends Controller
{

    /**
     * Event is triggered before creating new product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_BEFORE_CREATE_CATEGORY = 'beforeCreateCategory';
    /**
     * Event is triggered after creating new category.
     * Triggered with bl\cms\shop\backend\events\CategoryEvent.
     */
    const EVENT_AFTER_CREATE_CATEGORY = 'afterCreateCategory';
    /**
     * Event is triggered after editing category translation.
     * Triggered with bl\cms\shop\backend\events\CategoryEvent.
     */
    const EVENT_BEFORE_EDIT_CATEGORY = 'beforeEditCategory';
    /**
     * Event is triggered before editing category translation.
     * Triggered with bl\cms\shop\backend\events\CategoryEvent.
     */
    const EVENT_AFTER_EDIT_CATEGORY = 'afterEditCategory';
    /**
     * Event is triggered before deleting category.
     * Triggered with bl\cms\shop\backend\events\CategoryEvent.
     */
    const EVENT_BEFORE_DELETE_CATEGORY = 'beforeDeleteCategory';
    /**
     * Event is triggered after deleting category.
     * Triggered with bl\cms\shop\backend\events\CategoryEvent.
     */
    const EVENT_AFTER_DELETE_CATEGORY = 'afterDeleteCategory';

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
     * Renders list of all Category models.
     *
     * @return mixed
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
     * Deletes one category by id
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->trigger(self::EVENT_BEFORE_DELETE_CATEGORY);

        $category = Category::findOne($id);
        if (($category->delete())) {
            Yii::$app->getSession()->setFlash('success', 'The category has been successfully removed');
            $this->trigger(self::EVENT_AFTER_DELETE_CATEGORY,
                new CategoryEvent([
                    'id' => $id
                ])
            );
        } else {
            Yii::$app->getSession()->setFlash('error', 'Error deleting category');
        }

        if (\Yii::$app->request->isPjax) {
            return $this->actionIndex();
        } else return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param integer $languageId
     * @param integer $id
     *
     * @return mixed
     */
    public function actionSave($id = null, $languageId = null)
    {
        $category = (!empty($id)) ? Category::findOne($id) : new Category();
        $category_translation = (!empty($id)) ? CategoryTranslation::find()->where([
            'category_id' => $id,
            'language_id' => $languageId
        ])->one() : new CategoryTranslation();

        return $this->render('save', [
            'viewName' => 'add-basic',
            'params' => [
                'languages' => Language::findAll(['active' => true]),
                'maxPosition' => Category::find()->where(['parent_id' => $category->parent_id])->max('position') ?? 1,
                'category' => $category,
                'category_translation' => $category_translation,
                'categories' => Category::find()->with('translations')->all(),
                'selectedLanguage' => Language::findOne($languageId),
            ]
        ]);
    }

    /**
     * @param null|integer $id
     * @param null|integer $languageId
     * @param string $viewName
     * @param boolean $argumentsCanBeEmpty
     * @return mixed
     * @throws Exception
     *
     * Loads data from post.
     */
    private function loadCategory($id = null, $languageId = null, $viewName, $argumentsCanBeEmpty)
    {
        if (!$argumentsCanBeEmpty) {
            if (empty($id) || empty($languageId)) {
                throw new Exception('Category id and language id can not be empty');
            }
        }

        $category = (!empty($id)) ? Category::findOne($id) : new Category();
        $category_translation = (!empty($id)) ? CategoryTranslation::find()->where([
            'category_id' => $id,
            'language_id' => $languageId
        ])->one() : new CategoryTranslation();

        $category_translation = $category_translation ?? new CategoryTranslation();

        if (Yii::$app->request->isPost) {
            if ($category->isNewRecord) {
                $this->trigger(self::EVENT_BEFORE_CREATE_CATEGORY);
            } else {
                $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY);
            }

            $category->load(Yii::$app->request->post());
            $category_translation->load(Yii::$app->request->post());

            if ($category->validate()) {
                $eventName = $category->isNewRecord ? self::EVENT_AFTER_CREATE_CATEGORY : self::EVENT_AFTER_EDIT_CATEGORY;

                $category->save();

                $category_translation->category_id = $category->id;
                $category_translation->language_id = $languageId;

                if ($category_translation->validate()) {

                    $category_translation->save();

                    $this->trigger($eventName,
                        new CategoryEvent([
                            'id' => $category->id
                        ])
                    );

                    Yii::$app->getSession()->setFlash('success', 'The category has been successfully modified.');
                    return $this->redirect([$viewName, 'id' => $category->id, 'languageId' => $languageId]);
                }
            }
        }
        return $this->render('save', [
            'viewName' => $viewName,
            'params' => [
                'languages' => Language::findAll(['active' => true]),
                'maxPosition' => Category::find()->where(['parent_id' => $category->parent_id])->max('position') ?? 1,
                'category' => $category,
                'category_translation' => $category_translation,
                'categories' => Category::find()->with('translations')->all(),
                'selectedLanguage' => Language::findOne($languageId),
            ]
        ]);
    }

    /**
     * Adds category SEO data
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     */
    public function actionAddSeo($id, $languageId)
    {
        return $this->loadCategory($id, $languageId, 'add-seo', false);
    }

    /**
     * Basic category settings
     *
     * @param integer $languageId
     * @param integer $id
     * @return mixed
     */
    public function actionAddBasic($id = null, $languageId = null)
    {
        return $this->loadCategory($id, $languageId, 'add-basic', true);
    }


    /**
     * Adds category images
     *
     * @param integer $languageId
     * @param integer $categoryId
     * @return mixed
     * @throws Exception
     */
    public function actionAddImages($categoryId, $languageId)
    {
        if (empty($categoryId) || empty($languageId)) {
            throw new Exception('Category id and language id can not be empty');
        }

        $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY);

        $category = Category::findOne($categoryId);
        $image_form = new CategoryImageForm();

        if (Yii::$app->request->isPost) {
            $image_form->cover = UploadedFile::getInstance($image_form, 'cover');
            $image_form->thumbnail = UploadedFile::getInstance($image_form, 'thumbnail');
            $image_form->menu_item = UploadedFile::getInstance($image_form, 'menu_item');

            if (!empty($image_form->cover) || !empty($image_form->thumbnail) || !empty($image_form->menu_item)) {
                $image_name = $image_form->upload();

                $category->cover = (!empty($image_form->cover)) ? $image_name['cover'] : null;
                $category->thumbnail = (!empty($image_form->thumbnail)) ? $image_name['thumbnail'] : null;
                $category->menu_item = (!empty($image_form->menu_item)) ? $image_name['menu_item'] : null;
            }
            if ($category->validate()) {
                $category->save();
                $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                    new CategoryEvent([
                        'id' => $categoryId
                    ])
                );
                Yii::$app->getSession()->setFlash('success', 'The images have successfully uploaded.');
            }
            Yii::$app->getSession()->setFlash('error', 'Error loading image.');
        }

        return $this->render('save', [
            'viewName' => 'add-images',
            'params' => [
                'languages' => Language::findAll(['active' => true]),
                'category' => $category,
                'image_form' => $image_form,
                'selectedLanguage' => Language::findOne($languageId),
            ],
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
        $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY,
            new CategoryEvent());

        $category = Category::findOne($id);

        if (\Yii::$app->shop_imagable->delete('shop-category/' . $imageType, $category->$imageType)) {
            $category->$imageType = null;
            $category->save();
            Yii::$app->getSession()->setFlash('success', 'The image has been successfully deleted.');
            $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                new CategoryEvent([
                    'id' => $id
                ])
            );
        }
        else Yii::$app->getSession()->setFlash('error', 'Error deleting image.');

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

                $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                    new CategoryEvent([
                        'id' => $category->id
                    ])
                );

                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->render('save', [
            'viewName' => 'select-filters',
            'params' => [
                'languages' => Language::findAll(['active' => true]),
                'category' => $category,
                'selectedLanguage' => Language::findOne($languageId),
                'filters' => $filters,
                'newFilter' => new Filter(),
            ],
        ]);
    }

    /**
     * Deletes product filter from category
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFilter($id)
    {
        if (!empty($id)) {
            $filter = Filter::findOne($id);
            $filter->delete();
            $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                new CategoryEvent([
                    'id' => $filter->category_id
                ])
            );
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Changes category position to up
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUp($id)
    {
        if ($category = Category::findOne($id)) {
            $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY);

            $category->movePrev();
            $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                new CategoryEvent([
                    'id' => $id
                ])
            );
        }
        return $this->actionIndex();
    }

    /**
     * Changes category position to down
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDown($id)
    {
        if ($category = Category::findOne($id)) {
            $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY);
            $category->moveNext();
            $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                new CategoryEvent([
                    'id' => $id
                ])
            );
        }
        return $this->actionIndex();
    }

    /**
     * Shows or hides category
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSwitchShow($id)
    {
        $category = Category::findOne($id);
        if (!empty($category)) {
            $this->trigger(self::EVENT_BEFORE_EDIT_CATEGORY);
            $category->show = !$category->show;
            $category->save();
            $this->trigger(self::EVENT_AFTER_EDIT_CATEGORY,
                new CategoryEvent([
                    'id' => $id
                ])
            );
            return $this->actionIndex();
        } else throw new NotFoundHttpException();
    }
}