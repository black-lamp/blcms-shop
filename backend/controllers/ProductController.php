<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\backend\events\ProductEvent;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\SearchProduct;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ProductVideo;
use bl\multilang\entities\Language;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{

    /**
     * Event is triggered before creating new product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_BEFORE_CREATE_PRODUCT = 'beforeCreateProduct';
    /**
     * Event is triggered after creating new product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_CREATE_PRODUCT = 'afterCreateProduct';
    /**
     * Event is triggered after editing product translation.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_BEFORE_EDIT_PRODUCT= 'beforeEditProduct';
    /**
     * Event is triggered before editing product translation.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_EDIT_PRODUCT = 'afterEditProduct';
    /**
     * Event is triggered before deleting product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_BEFORE_DELETE_PRODUCT = 'beforeDeleteProduct';
    /**
     * Event is triggered after deleting product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_DELETE_PRODUCT = 'afterDeleteProduct';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'only' => ['save'],
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'roles' => ['viewProductList'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'save', 'add-basic',
                            'add-param', 'delete-param', 'update-param',
                            'add-image', 'delete-image',
                            'add-video', 'delete-video',
                            'add-price', 'remove-price',
                            'up', 'down', 'generate-seo-url'
                        ],
                        'roles' => ['createProduct', 'createProductWithoutModeration',
                            'updateProduct', 'updateOwnProduct'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['delete'],
                        'roles' => ['deleteProduct', 'deleteOwnProduct'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['change-product-status'],
                        'roles' => ['moderateProductCreation'],
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists Product models.
     *
     * DataProvider sends created by user product models for users which have 'viewProductList' permission
     * and for users which have 'viewCompleteProductList' permission it sends all product models.
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchProduct();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $notModeratedProductsCount = count(Product::find()->where(['status' => Product::STATUS_ON_MODERATION])->all());

        return $this->render('index', [
            'notModeratedProductsCount' => $notModeratedProductsCount,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'languages' => Language::findAll(['active' => true])
        ]);

    }

    /**
     * Creates or edits product model.
     *
     * Users which have 'updateOwnProduct' permission can edit only Product models that have been created by their.
     * Users which have 'updateProduct' permission can create and editing all Product models.
     * Users which have 'createProduct' permission can create Product models with status column equal to constant STATUS_ON_MODERATION.
     * Users which have 'createProductWithoutModeration' permission can create Product models with status column equal to constant STATUS_SUCCESS.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSave($id = null, $languageId = null)
    {
        if (!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        } else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($id)) {

            $product = Product::findOne($id);

            if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
                $products_translation = ProductTranslation::find()->where([
                    'product_id' => $id,
                    'language_id' => $languageId
                ])->one();
                if (empty($products_translation)) {
                    $products_translation = new ProductTranslation();
                }
            } else {
                throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to update this product.'));
            }
        } else {

            if (\Yii::$app->user->can('createProduct')) {

                $product = new Product();
                $products_translation = new ProductTranslation();
            } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to create new product.'));
        }

        return $this->render('save', [
            'viewName' => 'add-basic',
            'selectedLanguage' => $selectedLanguage,
            'product' => $product,
            'languages' => Language::find()->all(),

            'params' => [
                'selectedLanguage' => $selectedLanguage,
                'product' => $product,
                'products_translation' => $products_translation,
                'categories' => CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(),
                'params_translation' => new ParamTranslation(),
            ]
        ]);
    }

    /**
     * Deletes product model.
     *
     * Users which have 'deleteProduct' permission can delete all Product models.
     * Users which have 'deleteOwnProduct' permission can delete only Product models that have been created by their.
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        if (\Yii::$app->user->can('deleteProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $this->trigger(self::EVENT_BEFORE_DELETE_PRODUCT, new ProductEvent([
                'productId' => $id,
                'userId' => Yii::$app->user->id,
            ]));
            Product::deleteAll(['id' => $id]);
            $this->trigger(self::EVENT_AFTER_DELETE_PRODUCT, new ProductEvent([
                'productId' => $id,
                'userId' => Yii::$app->user->id,
            ]));
            return $this->redirect('index');
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to delete this product.'));
    }

    /**
     * Adds basic info for product model.
     *
     * Users which have 'updateOwnProduct' permission can add or edit basic info only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can can add or edit basic info for all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddBasic($id = null, int $languageId)
    {

        if (!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        } else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($id)) {
            $product = Product::findOne($id);

            if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
                $products_translation = ProductTranslation::find()->where([
                    'product_id' => $id,
                    'language_id' => $languageId
                ])->one();
                if (empty($products_translation)) {
                    $products_translation = new ProductTranslation();
                }
            } else throw new ForbiddenHttpException();

        } else {
            if (\Yii::$app->user->can('createProduct')) {

                $this->trigger(self::EVENT_BEFORE_CREATE_PRODUCT);

                $product = new Product();
                $products_translation = new ProductTranslation();
            } else throw new ForbiddenHttpException();
        }

        if (Yii::$app->request->isPost) {

            $product->load(Yii::$app->request->post());

            if ($product->isNewRecord) {
                $product->owner = Yii::$app->user->id;
                if (\Yii::$app->user->can('createProductWithoutModeration')) {
                    $product->status = Product::STATUS_SUCCESS;
                }
                if ($product->validate()) {
                    $product->save();

                    $this->trigger(self::EVENT_AFTER_CREATE_PRODUCT, new ProductEvent([
                        'productId' => $product->id,
                        'userId' => Yii::$app->user->id,
                        'time' => $product->creation_time
                    ]));
                }
            }

            $this->trigger(self::EVENT_BEFORE_EDIT_PRODUCT, new ProductEvent([
                'productId' => $product->id,
                'userId' => Yii::$app->user->id,
                'time' => $products_translation->update_time
            ]));
            $products_translation->load(Yii::$app->request->post());

            if ($product->validate() && $products_translation->validate()) {

                if (empty($products_translation->seoUrl)) {
                    $products_translation->seoUrl = Inflector::slug($products_translation->title);
                }

                $products_translation->product_id = $product->id;
                $products_translation->language_id = $selectedLanguage->id;
                $products_translation->save();
                $product->save();

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'productId' => $product->id,
                    'userId' => Yii::$app->user->id,
                    'time' => $products_translation->update_time
                ]));
            }
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('add-basic', [
                'languages' => Language::find()->all(),
                'selectedLanguage' => $selectedLanguage,
                'product' => $product,
                'products_translation' => $products_translation,
                'params_translation' => new ParamTranslation(),
            ]);
        } else {
            return $this->render('save', [
                'viewName' => 'add-basic',
                'selectedLanguage' => $selectedLanguage,
                'product' => $product,
                'languages' => Language::find()->all(),

                'params' => [
                    'languages' => Language::find()->all(),
                    'selectedLanguage' => $selectedLanguage,
                    'product' => $product,
                    'products_translation' => $products_translation,
                    'categories' => CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(),
                    'params_translation' => new ParamTranslation(),
                ]
            ]);
        }
    }

    /**
     * Adds params for product model.
     *
     * Users which have 'updateOwnProduct' permission can add params only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can add params for all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddParam($id = null, $languageId = null)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $param = new Param();
            $param->product_id = $id;
            $param_translation = new ParamTranslation();
            $param_translation->language_id = $languageId;

            if (Yii::$app->request->isPost) {

                $param->load(Yii::$app->request->post());
                $param_translation->load(Yii::$app->request->post());
                if ($param->validate() && $param_translation->validate()) {
                    $param->save();
                    $param_translation->param_id = $param->id;
                    $param_translation->language_id = $languageId;
                    $param_translation->save();
                    Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                } else
                    Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
            }

            $languages = Language::find()->all();
            $languageIndex = 0;
            foreach ($languages as $key => $language) {
                if ($language->id == $languageId)  {
                    $languageIndex = $key;
                    break;
                }
            }

            return $this->render('save', [
                'viewName' => 'add-param',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => Product::findOne($id),
                'languages' => Language::find()->all(),

                'params' => [
                    'params' => Param::find()->where([
                        'product_id' => $id,
                    ])->all(),
                    'param_translation' => new ParamTranslation(),
                    'productId' => $id,
                    'languageId' => $languageId,
                    'languageIndex' => $languageIndex
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Deletes param from product model.
     *
     * Users which have 'updateOwnProduct' permission can delete params only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can delete params for all Product models.
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDeleteParam($id)
    {
        $param = Param::findOne($id);
        $param->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Update param translation.
     * Users which have 'updateOwnProduct' permission can update params only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can update params for all Product models.
     *
     * @param int $id
     * @param int $languageId
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdateParam(int $id, int $languageId) {
        if (!empty($id) && !empty($languageId)) {
            $paramTranslation = ParamTranslation::find()
                ->where(['param_id' => $id, 'language_id' => $languageId])->one();

            if (empty($paramTranslation)) {
                $paramTranslation = new ParamTranslation();
                $paramTranslation->param_id = $id;
                $paramTranslation->language_id = $languageId;
            }

            if (Yii::$app->request->isPost) {
                $paramTranslation->load(Yii::$app->request->post());
                if ($paramTranslation->validate()) {
                    $paramTranslation->save();
                    Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                } else {
                    Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
                }

                return $this->redirect(['add-param', 'id' => $paramTranslation->param->product_id, 'languageId' => $languageId,]);
            }

            return $this->renderPartial('update-param', [
                'paramTranslation' => $paramTranslation,
                'languageId' => $languageId
            ]);
        }
        else throw new NotFoundHttpException();
    }

    /**
     * Changes product position to up
     *
     * Users which have 'updateOwnProduct' permission can change position only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can change position for all Product models.
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUp($id)
    {
        $product = Product::findOne($id);
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
            if (!empty($product)) {
                $product->movePrev();
            }
            return $this->actionIndex();
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Changes product position to down
     *
     * Users which have 'updateOwnProduct' permission can change position only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can change position for all Product models.
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDown($id)
    {
        $product = Product::findOne($id);
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {

            if ($product) {
                $product->moveNext();
            }
            return $this->actionIndex();
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can add image only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can add image for all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddImage($id, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $product = Product::findOne($id);
            $image_form = new ProductImageForm();
            $image = new ProductImage();

            if (Yii::$app->request->isPost) {

                $image_form->load(Yii::$app->request->post());
                $image_form->image = UploadedFile::getInstance($image_form, 'image');

                if (!empty($image_form->image)) {
                    $uploadedImageName = $image_form->upload();

                    $image->file_name = $uploadedImageName;
                    $image->alt = $image_form->alt2;
                    $image->product_id = $product->id;
                    if ($image->validate()) {
                        $image->save();
                    } else die(var_dump($image->errors));
                }
                if (!empty($image_form->link)) {
                    $image_name = $image_form->copy($image_form->link);
                    $image->file_name = $image_name;
                    $image->alt = $image_form->alt1;
                    $image->product_id = $product->id;
                    if ($image->validate()) {
                        $image->save();
                    }
                }
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-image', [
                    'selectedLanguage' => Language::findOne($languageId),
                    'product' => $product,
                    'image_form' => new ProductImageForm()
                ]);
            }
            return $this->render('save', [
                'languages' => Language::find()->all(),
                'viewName' => 'add-image',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,

                'params' => [
                    'selectedLanguage' => Language::findOne($languageId),
                    'product' => $product,
                    'image_form' => new ProductImageForm()
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can delete image only from Product models that have been created by their.
     * Users which have 'updateProduct' permission can delete image from all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionDeleteImage($id, $languageId)
    {
        if (!empty($id)) {
            $image = ProductImage::find()->where(['id' => $id])->one();
            if (!empty($image)) {
                $product = Product::findOne($image->product_id);
                if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
                    $product_image = new ProductImage();
                    $product_image->removeImage($id);

                    if (\Yii::$app->request->isPjax) {
                        return $this->renderPartial('add-image', [
                            'selectedLanguage' => Language::findOne($languageId),
                            'product' => $product,
                            'image_form' => new ProductImageForm()
                        ]);
                    } else return $this->redirect(\Yii::$app->request->referrer);
                } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
            }
        } else throw new Exception();
    }

    /**
     * Users which have 'updateOwnProduct' permission can add video only from Product models that have been created by their.
     * Users which have 'updateProduct' permission can add video from all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddVideo($id, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $product = Product::findOne($id);
            $video = new ProductVideo();
            $videoForm = new ProductVideoForm();

            if (Yii::$app->request->isPost) {

                $video->load(Yii::$app->request->post());

                $videoForm->load(Yii::$app->request->post());
                $videoForm->file_name = UploadedFile::getInstance($videoForm, 'file_name');
                if ($fileName = $videoForm->upload()) {
                    $video->file_name = $fileName;
                    $video->resource = 'videofile';
                    $video->product_id = $id;
                    $video->save();
                }

                if ($video->resource == 'youtube') {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video->file_name, $match)) {
                        $id = $match[1];
                        $video->product_id = $product->id;
                        $video->file_name = $id;
                        if ($video->validate()) {
                            $video->save();
                        }
                    } else {
                        \Yii::$app->session->setFlash('error', \Yii::t('shop', 'Sorry, this format is not supported'));
                    }
                } elseif ($video->resource == 'vimeo') {
                    $regexstr = '~
                        # Match Vimeo link and embed code
                        (?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
                        (?:							        # Group vimeo url
                            https?:\/\/				        # Either http or https
                            (?:[\w]+\.)*			        # Optional subdomains
                            vimeo\.com				        # Match vimeo.com
                            (?:[\/\w]*\/videos?)?	        # Optional video sub directory this handles groups links also
                            \/						        # Slash before Id
                            ([0-9]+)				        # $1: VIDEO_ID is numeric
                            [^\s]*					        # Not a space
                        )							        # End group
                        "?							        # Match end quote if part of src
                        (?:[^&gt;]*&gt;&lt;/iframe&gt;)?	# Match the end of the iframe
                        (?:&lt;p&gt;.*&lt;/p&gt;)?		    # Match any title information stuff
                        ~ix';
                    if (preg_match($regexstr, $video->file_name, $match)) {
                        $id = $match[1];
                        $video->product_id = $product->id;
                        $video->file_name = $id;
                        if ($video->validate()) {
                            $video->save();
                        }
                    } else {
                        \Yii::$app->session->setFlash('error', \Yii::t('shop', 'Sorry, this format is not supported'));
                    }
                }
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-video', [
                    'product' => $product,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form' => new ProductVideo(),
                    'video_form_upload' => new ProductVideoForm(),
                    'videos' => ProductVideo::find()->where(['product_id' => $product->id])->all()
                ]);
            }

            return $this->render('save', [
                'viewName' => 'add-video',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'languages' => Language::find()->all(),

                'params' => [
                    'product' => $product,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form' => new ProductVideo(),
                    'video_form_upload' => new ProductVideoForm(),
                    'videos' => ProductVideo::find()->where(['product_id' => $product->id])->all()
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can delete video only from Product models that have been created by their.
     * Users which have 'updateProduct' permission can delete video from all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDeleteVideo($id, $languageId)
    {
        if (!empty($id)) {
            $video = ProductVideo::findOne($id);
            $product = Product::findOne($video->product_id);

            if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {

                if ($video->resource == 'videofile') {
                    $dir = Yii::getAlias('@frontend/web/video');
                    unlink($dir . '/' . $video->file_name);
                }
                ProductVideo::deleteAll(['id' => $id]);

                return $this->renderPartial('add-video', [
                    'product' => $product,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form' => new ProductVideo(),
                    'video_form_upload' => new ProductVideoForm(),
                    'videos' => ProductVideo::find()->where(['product_id' => $product->id])->all()
                ]);
            } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));

        }
        return false;
    }

    /**
     * Users which have 'updateOwnProduct' permission can add price only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can add price for all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAddPrice($id, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $price = new ProductPrice();
            $priceTranslation = new ProductPriceTranslation();

            $product = Product::findOne($id);
            $selectedLanguage = Language::findOne($languageId);

            if (\Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();
                if ($price->load($post) && $priceTranslation->load($post)) {
                    $price->product_id = $product->id;
                    if ($price->save()) {
                        $priceTranslation->price_id = $price->id;
                        $priceTranslation->language_id = $selectedLanguage->id;
                        if ($priceTranslation->save()) {
                            $price = new ProductPrice();
                            $priceTranslation = new ProductPriceTranslation();
                        }
                    }
                }
            }
            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-price', [
                    'priceList' => $product->prices,
                    'priceModel' => $price,
                    'priceTranslationModel' => $priceTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]);
            }
            return $this->render('save', [
                'viewName' => 'add-price',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'languages' => Language::find()->all(),

                'params' => [
                    'priceList' => $product->prices,
                    'priceModel' => $price,
                    'priceTranslationModel' => $priceTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can delete price only from Product models that have been created by their.
     * Users which have 'updateProduct' permission can delete price from all Product models.
     *
     * @param integer $priceId
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionRemovePrice($priceId, $id, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            ProductPrice::deleteAll(['id' => $priceId]);
            return $this->actionAddPrice($id, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Changes product status property by ModerationManager
     *
     * Users which have 'moderateProductCreation' permission can change product status.
     *
     * @param integer $id
     * @param integer $status
     * @return mixed
     */
    public function actionChangeProductStatus($id, $status)
    {
        if (!empty($id) && !empty($status)) {
            $product = Product::findOne($id);
            if ($product->status == Product::STATUS_ON_MODERATION) {

                switch ($status) {
                    case Product::STATUS_SUCCESS:
                        $product->status = Product::STATUS_SUCCESS;
                        $product->save();
                        break;
                    case Product::STATUS_DECLINED:
                        $product->status = Product::STATUS_DECLINED;
                        $product->save();
                        break;
                }
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Generates seo Url from title on add-basic page
     *
     * @param string $title
     * @return string
     */
    public function actionGenerateSeoUrl($title)
    {
        return Inflector::slug($title);
    }
}


