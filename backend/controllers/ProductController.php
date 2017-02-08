<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\components\user\models\UserGroup;
use Yii;
use yii\base\{
    Exception, Model
};
use yii\helpers\{
    ArrayHelper, Inflector, Url
};
use yii\filters\AccessControl;
use bl\multilang\entities\Language;
use bl\cms\shop\backend\components\events\ProductEvent;
use yii\web\{
    Controller, ForbiddenHttpException, NotFoundHttpException, UploadedFile
};
use bl\cms\shop\backend\components\form\{
    CombinationAttributeForm, CombinationImageForm, ProductFileForm, ProductImageForm, ProductVideoForm
};
use bl\cms\shop\common\entities\{
    Category, CategoryTranslation, CombinationPrice, CombinationTranslation, ParamTranslation, Product, ProductAdditionalProduct, Combination, CombinationAttribute, CombinationImage, ProductFile, ProductFileTranslation, ProductImage, ProductImageTranslation, Price, ProductPrice, SearchProduct, ProductTranslation, ProductVideo
};

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
    const EVENT_BEFORE_EDIT_PRODUCT = 'beforeEditProduct';
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
                            'add-image', 'delete-image',
                            'add-video', 'delete-video',
                            'add-file', 'remove-file',
                            'up', 'down', 'generate-seo-url',
                            'additional-product'
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
     * @return mixed
     */
    public function actionAdditionalProduct()
    {
        $additionalProductsCategories = Category::find()->with('products')
            ->where(['additional_products' => true])->all();

        return $this->render('additional-product', [
            'additionalProductsCategories' => $additionalProductsCategories
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

        $prices = [];
        $userGroups = UserGroup::find()->all();
        foreach ($userGroups as $userGroup) {
            $price = Price::find()->joinWith('productPrice')
                ->where(['product_id' => $product->id, 'user_group_id' => $userGroup->id])->one();
            if (empty($price)) {
                $price = new Price();
                $price->save();
                $productPrice = new ProductPrice();
                $productPrice->user_group_id = $userGroup->id;
                $productPrice->product_id = $product->id;
                $productPrice->price_id = $price->id;
                if ($productPrice->validate()) $productPrice->save();

            }
            $prices[$price->id] = $price;
        }

        return $this->render('save', [
            'viewName' => 'add-basic',
            'selectedLanguage' => $selectedLanguage,
            'product' => $product,
            'languages' => Language::find()->all(),

            'params' => [
                'prices' => $prices,
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
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $product = Product::findOne($id);
        if (empty($product)) {
            throw new NotFoundHttpException();
        }

        if (\Yii::$app->user->can('deleteProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $this->trigger(self::EVENT_BEFORE_DELETE_PRODUCT);

            $product->delete();

            $this->trigger(self::EVENT_AFTER_DELETE_PRODUCT, new ProductEvent([
                'id' => $id
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
    public function actionAddBasic($id = null, $languageId = null)
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

        $prices = [];
        $userGroups = UserGroup::find()->all();
        foreach ($userGroups as $userGroup) {
            $price = Price::find()->joinWith('productPrice')
                ->where(['product_id' => $product->id, 'user_group_id' => $userGroup->id])->one();
            if (empty($price)) {
                $price = new Price();
                if ($price->validate()) $price->save();
                $productPrice = new ProductPrice();
                $productPrice->user_group_id = $userGroup->id;
                $productPrice->product_id = $product->id;
                $productPrice->price_id = $price->id;
                if ($productPrice->validate()) $productPrice->save();

            }
            $prices[$price->id] = $price;
        }

        if (Yii::$app->request->isPost) {

            $product->load(Yii::$app->request->post());
            $product->category_id = (!empty($product->category_id)) ? $product->category_id : null;
            if ($product->isNewRecord) {

                $eventName = self::EVENT_AFTER_CREATE_PRODUCT;

                $product->owner = Yii::$app->user->id;
                if (\Yii::$app->user->can('createProductWithoutModeration')) {
                    $product->status = Product::STATUS_SUCCESS;
                }
                if ($product->validate()) {
                    $product->save();
                }
            } else {
                $eventName = self::EVENT_AFTER_EDIT_PRODUCT;
            }

            $this->trigger(self::EVENT_BEFORE_EDIT_PRODUCT);

            $old_productTitle = $products_translation->title;
            $products_translation->load(Yii::$app->request->post());

            if ($product->validate() && $products_translation->validate()) {

                if (empty($products_translation->seoUrl) || ($products_translation->title != $old_productTitle)) {
                    $products_translation->seoUrl = Inflector::slug($products_translation->title);
                }

                $products_translation->product_id = $product->id;
                $products_translation->language_id = $selectedLanguage->id;
                $products_translation->save();
                $product->save();

                if (Model::loadMultiple($prices, Yii::$app->request->post()) && Model::validateMultiple($prices)) {
                    foreach ($prices as $price) {
                        $price->save(false);
                    }
                }
                $this->trigger($eventName, new ProductEvent([
                    'id' => $product->id
                ]));

                return $this->redirect(Url::to(['add-basic', 'id' => $product->id, 'languageId' => $selectedLanguage->id]));
            }
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('add-basic', [
                'prices' => $prices,
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
                    'prices' => $prices,
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
                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $id
                ]));
            }
            return $this->redirect(\Yii::$app->request->referrer);
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
                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $id
                ]));
            }
            return $this->redirect(\Yii::$app->request->referrer);
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
            $image_form = new ProductImageForm();
            $image = new ProductImage();
            $imageTranslation = new ProductImageTranslation();

            if (Yii::$app->request->isPost) {

                $image_form->load(Yii::$app->request->post());
                $image_form->image = UploadedFile::getInstance($image_form, 'image');

                if (!empty($image_form->image)) {
                    if ($uploadedImageName = $image_form->upload()) {

                        $image->file_name = $uploadedImageName;
                        $imageTranslation->alt = $image_form->alt2;
                        $image->product_id = $id;
                        if ($image->validate()) {
                            $image->save();
                            $imageTranslation->image_id = $image->id;
                            $imageTranslation->language_id = $languageId;
                            if ($imageTranslation->validate()) {
                                $imageTranslation->save();
                            }
                        }
                    }
                }
                if (!empty($image_form->link)) {
                    $image_name = $image_form->copy($image_form->link);
                    $image->file_name = $image_name;
                    $imageTranslation->alt = $image_form->alt1;
                    $image->product_id = $id;
                    if ($image->validate()) {
                        $image->save();
                        $imageTranslation->image_id = $image->id;
                        $imageTranslation->language_id = $languageId;
                        if ($imageTranslation->validate()) {
                            $imageTranslation->save();
                        }
                    }
                }
                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $id
                ]));
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-image', [
                    'selectedLanguage' => Language::findOne($languageId),
                    'productId' => $id,
                    'image_form' => new ProductImageForm(),
                    'images' => ProductImage::find()->where(['product_id' => $id])->orderBy('position')->all(),
                ]);
            }
            return $this->render('save', [
                'languages' => Language::find()->all(),
                'viewName' => 'add-image',
                'selectedLanguage' => Language::findOne($languageId),
                'productId' => $id,
                'product' => Product::findOne($id),

                'params' => [
                    'selectedLanguage' => Language::findOne($languageId),
                    'productId' => $id,
                    'image_form' => new ProductImageForm(),
                    'images' => ProductImage::find()->where(['product_id' => $id])->orderBy('position')->all(),
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    public function actionEditImage($id, $languageId)
    {
        $image = ProductImage::findOne($id);
        $imageTranslation = ProductImageTranslation::find()->where([
            'image_id' => $id,
            'language_id' => $languageId
        ])->one();
        if (empty($imageTranslation)) {
            $imageTranslation = new ProductImageTranslation();
        }

        if (Yii::$app->request->isPost) {

            $imageTranslation->load(Yii::$app->request->post());
            $imageTranslation->image_id = $id;
            $imageTranslation->language_id = $languageId;

            if ($imageTranslation->validate()) {
                $imageTranslation->save();


                return $this->redirect(Url::to(['add-image',
                    'id' => $image->product_id,
                    'languageId' => $languageId
                ]));
            } else die(var_dump($imageTranslation->errors));
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('edit-image', [
                'selectedLanguage' => Language::findOne($languageId),
                'imageTranslation' => $imageTranslation,
                'image' => $image
            ]);
        }

        return $this->render('save', [
            'languages' => Language::find()->all(),
            'viewName' => 'edit-image',
            'selectedLanguage' => Language::findOne($languageId),
            'productId' => $image->product_id,
            'product' => Product::findOne($image->product_id),

            'params' => [
                'selectedLanguage' => Language::findOne($languageId),
                'imageTranslation' => $imageTranslation,
                'image' => $image
            ]
        ]);
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
                    $image->removeImage($id);

                    $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                        'id' => $image->product_id
                    ]));

                    return $this->redirect(['add-image', 'id' => $image->product_id, 'languageId' => $languageId]);

                } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
            }
        } else throw new Exception();
    }

    /**
     * Changes ProductImage position to down
     *
     * Users which have 'updateOwnProduct' permission can change position only for ProductImage models that have been created by their.
     * Users which have 'updateProduct' permission can change position for all ProductImage models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageDown($id, $languageId)
    {
        $productImage = ProductImage::findOne($id);
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productImage->product_id)->owner])) {

            if ($productImage) {
                $productImage->moveNext();
                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $productImage->product_id
                ]));
            }
            return $this->actionAddImage($productImage->product_id, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Changes ProductImage position to up
     *
     * Users which have 'updateOwnProduct' permission can change position only for ProductImage models that have been created by their.
     * Users which have 'updateProduct' permission can change position for all ProductImage models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageUp($id, $languageId)
    {
        $productImage = ProductImage::findOne($id);
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productImage->product_id)->owner])) {

            if ($productImage) {
                $productImage->movePrev();

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $productImage->product_id
                ]));
            }
            return $this->actionAddImage($productImage->product_id, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
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
                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $id
                ]));
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-video', [
                    'product' => $product,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form' => new ProductVideo(),
                    'video_form_upload' => new ProductVideoForm(),
                    'video' => $video,
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
                    'video' => $video,
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

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $video->product_id
                ]));

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
     * Users which have 'updateOwnProduct' permission can add file only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can add file for all Product models.
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionAddFile($id, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($id)->owner])) {
            $file = new ProductFile();
            $fileTranslation = new ProductFileTranslation();
            $fileForm = new ProductFileForm();

            $product = Product::findOne($id);
            $selectedLanguage = Language::findOne($languageId);

            if (\Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();

                if ($fileForm->load($post) && $fileTranslation->load($post)) {

                    $fileForm->file = UploadedFile::getInstance($fileForm, 'file');

                    $fileName = $fileForm->upload();

                    $file->file = $fileName;
                    $file->product_id = $product->id;

                    if ($file->save()) {
                        $fileTranslation->product_file_id = $file->id;
                        $fileTranslation->language_id = $selectedLanguage->id;

                        if (!$fileTranslation->save())
                            throw new Exception(var_dump($fileTranslation->errors));
                        $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                            'id' => $file->product_id
                        ]));
                    }
                }
            }
            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-file', [
                    'fileList' => $product->files,
                    'fileModel' => $fileForm,
                    'fileTranslationModel' => $fileTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]);
            }
            return $this->render('save', [
                'viewName' => 'add-file',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'languages' => Language::find()->all(),

                'params' => [
                    'fileList' => $product->files,
                    'fileModel' => $fileForm,
                    'fileTranslationModel' => $fileTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can add file only for Product models that have been created by their.
     * Users which have 'updateProduct' permission can add file for all Product models.
     *
     * @param integer $productId
     * @param integer $fileId
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionUpdateFile($productId, $fileId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {

            $file = ProductFile::findOne(['id' => $fileId]);
            if (!empty($file)) {
                $product = $file->product;

                if (empty($file->getTranslation($languageId))) {
                    $fileTranslation = new ProductFileTranslation();
                    $fileTranslation->language_id = $languageId;
                    $fileTranslation->product_file_id = $fileId;
                }
                else {
                    $fileTranslation = $file->getTranslation($languageId);
                }
            }
            else throw new NotFoundHttpException();

            $fileForm = new ProductFileForm();

            $selectedLanguage = Language::findOne($languageId);

            if (\Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();

                if ($fileTranslation->load($post)) {

                    if ($file->save()) {
                        $fileTranslation->product_file_id = $file->id;
                        $fileTranslation->language_id = $selectedLanguage->id;

                        if (!$fileTranslation->save())
                            throw new Exception(var_dump($fileTranslation->errors));
                        $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                            'id' => $file->product_id
                        ]));

                        return $this->redirect(['/shop/product/add-file', 'id' => $productId, 'languageId' => $languageId]);
                    }
                }
            }
            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('update-file', [
                    'fileList' => $product->files,
                    'fileModel' => $fileForm,
                    'fileTranslationModel' => $fileTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]);
            }
            return $this->render('save', [
                'viewName' => 'update-file',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'languages' => Language::find()->all(),

                'params' => [
                    'fileModel' => $fileForm,
                    'fileTranslationModel' => $fileTranslation,
                    'product' => $product,
                    'languages' => Language::findAll(['active' => true]),
                    'language' => $selectedLanguage
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    /**
     * Users which have 'updateOwnProduct' permission can delete file only from Product models that have been created by their.
     * Users which have 'updateProduct' permission can delete file from all Product models.
     *
     * @param integer $fileId
     * @param integer $productId
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionRemoveFile($fileId, $productId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            ProductFile::deleteAll(['id' => $fileId]);
            $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                'id' => $productId
            ]));
            return $this->actionAddFile($productId, $languageId);
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

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $id
                ]));
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Adds new combination
     *
     * @param int $productId
     * @param int $languageId
     * @return mixed
     */
    public function actionAddCombination(int $productId, int $languageId)
    {
        $combination = new Combination();
        $combinationTranslation = new CombinationTranslation();
        $combinationsList = Combination::find()->where(['product_id' => $productId])->all();

        $imageForm = new CombinationImageForm();
        $productImages = ProductImage::find()->where(['product_id' => $productId])->all();

        $combinationAttributeForm = new CombinationAttributeForm();
        $combinationAttribute = new CombinationAttribute();

        $userGroups = UserGroup::find()->all();

        $prices = [];
        foreach ($userGroups as $userGroup) {
            $price = new Price();
            $prices[$userGroup->id] = $price;
        }

        if (\Yii::$app->request->isPost) {

            $post = \Yii::$app->request->post();
            if ($combination->load($post)) {
                $combination->product_id = $productId;

                $combination->setDefaultOrNotDefault();
                if ($combination->validate()) $combination->save();

                if ($combinationTranslation->load($post)) {
                    $combinationTranslation->combination_id = $combination->id;
                    $combinationTranslation->language_id = $languageId;
                    if ($combinationTranslation->validate()) $combinationTranslation->save();
                }

                if (Model::loadMultiple($prices, Yii::$app->request->post()) && Model::validateMultiple($prices)) {
                    foreach ($prices as $key => $price) {
                        $price->save(false);
                        $combinationPrice = new CombinationPrice();
                        $combinationPrice->combination_id = $combination->id;
                        $combinationPrice->price_id = $price->id;
                        $combinationPrice->user_group_id = $key;
                        if ($combinationPrice->validate()) $combinationPrice->save();
                    }
                }

                $this->loadCombinationAttributeForm($combination, $post, $combinationAttributeForm, $combinationAttribute);
                $this->saveCombinationImages($imageForm, $post, null, $combination);

                $eventName = self::EVENT_AFTER_EDIT_PRODUCT;
                $this->trigger($eventName, new ProductEvent([
                    'id' => $combination->product_id
                ]));

                $this->redirect(['add-combination',
                    'productId' => $productId,
                    'languageId' => $languageId
                ]);
            }
        }

        return $this->render('save', [
            'viewName' => 'add-combination',
            'selectedLanguage' => Language::findOne($languageId),
            'product' => Product::findOne($productId),
            'languages' => Language::find()->all(),

            'params' => [
                'combination' => $combination,
                'combinationTranslation' => $combinationTranslation,
                'combinations' => $combinationsList,
                'product' => Product::findOne($productId),
                'productImages' => $productImages,
                'image_form' => $imageForm,
                'languageId' => $languageId,
                'combinationAttribute' => $combinationAttribute,
                'combinationAttributeForm' => $combinationAttributeForm,
                'prices' => $prices,
            ]
        ]);
    }

    /**
     * @param $imageForm CombinationImageForm
     * @param $post array
     * @param $combinationImages array
     * @param $combination Combination
     * @param $newCombination boolean
     * @param $productImages ProductImage|null
     */
    private function saveCombinationImages($imageForm, $post, $combinationImages,
                                           $combination, $newCombination = true, $productImages = null)
    {
        if ($imageForm->load($post)) {

            if (!empty($imageForm->product_image_id)) {
                if ($imageForm->validate()) {

                    if (!$newCombination) {
                        /*Deleting*/
                        foreach ($combinationImages as $combinationImage) {
                            if (!in_array($combinationImage->id, $imageForm->product_image_id)) {
                                CombinationImage::deleteAll(['id' => $combinationImage->id]);
                            }
                        }
                    }

                    foreach ($imageForm->product_image_id as $image) {
                        if (!$newCombination) {
                            /*Adding*/
                            if (!in_array($image, ArrayHelper::toArray($combinationImages))) {
                                $newImage = new CombinationImage();
                                $newImage->product_image_id = $image;
                                $newImage->combination_id = $combination->id;
                                if ($newImage->validate()) $newImage->save();

                                CombinationImage::deleteAll(['id' => $image]);
                            }
                        } else {
                            $newCombinationImage = new CombinationImage();

                            $newCombinationImage->combination_id = (int)$combination->id;
                            $newCombinationImage->product_image_id = (int)$image;
                            if ($newCombinationImage->validate()) {
                                $newCombinationImage->save();
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * @param $combination
     * @param $post
     * @param $combinationAttributeForm CombinationAttributeForm
     * @param $combinationAttribute CombinationAttribute
     */
    private function loadCombinationAttributeForm($combination, $post, $combinationAttributeForm, $combinationAttribute)
    {
        if ($combinationAttributeForm->load($post)) {
            if ($combinationAttributeForm->validate()) {
                foreach ($combinationAttributeForm->attribute_id as $key => $attributeId) {

                    if (!empty($attributeId)) {
                        $combinationAttribute->combination_id = $combination->id;
                        $combinationAttribute->attribute_id = (int)$attributeId;
                        $combinationAttribute->attribute_value_id =
                            (int)$combinationAttributeForm->attribute_value_id[$key];

                        if ($combinationAttribute->validate()) {
                            $combinationAttribute->save();
                            $combinationAttribute = new CombinationAttribute();
                        }
                    }
                }
            }
        }
    }

    /**
     * Updates combination
     *
     * @param int $combinationId
     * @param int $languageId
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionEditCombination(int $combinationId, int $languageId)
    {
        $combination = Combination::findOne($combinationId);
        if (empty($combination)) throw new NotFoundHttpException();

        $combinationTranslation = CombinationTranslation::find()->where([
            'combination_id' => $combination->id, 'language_id' => $languageId
        ])->one();
        if (empty($combinationTranslation)) {
            $combinationTranslation = new CombinationTranslation();
            $combinationTranslation->combination_id = $combination->id;
            $combinationTranslation->language_id = $languageId;
        }

        $imageForm = new CombinationImageForm();
        $productImages = ProductImage::find()->where(['product_id' => $combination->product_id])->all();
        $combinationImages = CombinationImage::find()->where(['combination_id' => $combination->id])->all();

        $combinationAttributeForm = new CombinationAttributeForm();
        $combinationAttributes = CombinationAttribute::find()
            ->where(['combination_id' => $combination->id])->all();

        $prices = [];
        $userGroups = UserGroup::find()->all();
        foreach ($userGroups as $userGroup) {
            $price = Price::find()->joinWith('combinationPrice')
                ->where(['combination_id' => $combination->id, 'user_group_id' => $userGroup->id])->one();
            if (empty($price)) {
                $price = new Price();
                if ($price->validate()) $price->save();
                $combinationPrice = new CombinationPrice();
                $combinationPrice->combination_id = $combination->id;
                $combinationPrice->user_group_id = $userGroup->id;
                $combinationPrice->price_id = $price->id;
                if ($combinationPrice->validate()) $combinationPrice->save();

            }
            $prices[$price->id] = $price;
        }

        if (\Yii::$app->request->isPost) {

            $this->trigger(self::EVENT_BEFORE_EDIT_PRODUCT, new ProductEvent([
                'id' => $combination->product_id
            ]));

            $post = \Yii::$app->request->post();
            if ($combination->load($post)) {
                if ($combination->validate()) $combination->save();

                $combination->setDefaultOrNotDefault();

                if ($combinationTranslation->load($post) && $combinationTranslation->validate())
                    $combinationTranslation->save();

                /*Saving prices*/
                if (Model::loadMultiple($prices, Yii::$app->request->post()) && Model::validateMultiple($prices)) {
                    foreach ($prices as $price) {
                        $price->save(false);
                    }
                }

                $this->loadCombinationAttributeForm($combination, $post, $combinationAttributeForm, new CombinationAttribute());
                $this->saveCombinationImages($imageForm, $post, $combinationImages, $combination, false, $productImages);

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $combination->product_id
                ]));

                $this->redirect(['add-combination',
                    'productId' => $combination->product_id,
                    'languageId' => $languageId
                ]);
            }
        }

        return $this->render('save', [
            'viewName' => 'edit-combination',
            'selectedLanguage' => Language::findOne($languageId),
            'product' => Product::findOne($combination->product_id),
            'languages' => Language::find()->all(),

            'params' => [
                'combination' => $combination,
                'combinationTranslation' => $combinationTranslation,
                'product' => Product::findOne($combination->product_id),
                'productImages' => $productImages,

                'image_form' => $imageForm,
                'combinationImagesIds' => ArrayHelper::getColumn($combinationImages, 'product_image_id'),

                'languageId' => $languageId,
                'combinationAttributes' => $combinationAttributes,
                'combinationAttributeForm' => $combinationAttributeForm,
                'prices' => $prices
            ]
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDeleteCombinationAttribute(int $id)
    {
        CombinationAttribute::deleteAll(['id' => $id]);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $combinationId
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionChangeDefaultCombination($combinationId)
    {

        $combination = Combination::findOne($combinationId);

        if (!$combination->default) {
            $combination->findDefaultCombinationAndUndefault();

            $combination->default = !$combination->default;
            if ($combination->validate()) $combination->save();

            return $this->redirect(\Yii::$app->request->referrer);
        } else throw new Exception('Product must have one default combination');
    }

    /**
     * Removes combination
     *
     * @param int $combinationId
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRemoveCombination(int $combinationId)
    {
        $combination = Combination::findOne($combinationId);
        $productId = $combination->product_id;

        if (!empty($combination)) {
            $combination->delete();
            if (Combination::find()->where(['product_id' => $productId])->count() == 1) {
                $combination = Combination::find()->where(['product_id' => $productId])->one();
                $combination->default = 1;
                if ($combination->validate()) $combination->save();
            }

            $eventName = self::EVENT_AFTER_EDIT_PRODUCT;
            $this->trigger($eventName, new ProductEvent([
                'id' => $combination->product_id
            ]));

        } else throw new NotFoundHttpException();

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param int $combinationAttributeId
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRemoveCombinationAttribute(int $combinationAttributeId)
    {
        $combinationAttribute = CombinationAttribute::findOne($combinationAttributeId);
        if (empty($combinationAttribute)) throw new NotFoundHttpException();

        $combinationAttribute->delete();

        $eventName = self::EVENT_AFTER_EDIT_PRODUCT;
        $this->trigger($eventName, new ProductEvent([
            'id' => $combinationAttribute->combination->product_id
        ]));

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Adds new combination
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return mixed
     */
    public function actionAddAdditional(int $productId, int $languageId)
    {
        $additionalProductsCategories = Category::find()->with('products')
            ->where(['additional_products' => true])->all();

        $productAdditionalProducts = ProductAdditionalProduct::find()->where(['product_id' => $productId])->all();
        return $this->render('save', [
            'viewName' => 'add-additional',
            'selectedLanguage' => Language::findOne($languageId),
            'product' => Product::findOne($productId),
            'languages' => Language::find()->all(),

            'params' => [
                'additionalProductsCategories' => $additionalProductsCategories,
                'productAdditionalProducts' => $productAdditionalProducts,
                'productId' => $productId
            ]
        ]);
    }

    /**
     * @param $productId
     * @param $additionalProductId
     * @return bool
     * @throws Exception
     */
    public function actionAddToAdditionalProducts($productId, $additionalProductId)
    {
        $productAdditionalProduct = ProductAdditionalProduct::find()
            ->where(['product_id' => $productId, 'additional_product_id' => $additionalProductId])->one();
        if (empty($productAdditionalProduct)) {
            $productAdditionalProduct = new ProductAdditionalProduct();
            $productAdditionalProduct->product_id = $productId;
            $productAdditionalProduct->additional_product_id = $additionalProductId;

            if ($productAdditionalProduct->validate()) {
                $productAdditionalProduct->save();
                return true;
            }
        }
        throw new Exception();
    }

    public function actionRemoveAdditionalProduct($id)
    {
        $productAdditionalProduct = ProductAdditionalProduct::findOne($id);
        if (!empty($productAdditionalProduct)) $productAdditionalProduct->delete();
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(\Yii::$app->request->referrer);
        }
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