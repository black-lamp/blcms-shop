<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\components\user\models\UserGroup;
use Yii;
use yii\base\{
    Exception, Model
};
use yii\helpers\{
    Inflector, Url
};
use yii\filters\AccessControl;
use bl\multilang\entities\Language;
use bl\cms\shop\backend\components\events\ProductEvent;
use yii\web\{
    Controller, ForbiddenHttpException, NotFoundHttpException, UploadedFile
};
use bl\cms\shop\backend\components\form\{
    ProductFileForm, ProductImageForm, ProductVideoForm
};
use bl\cms\shop\common\entities\{
    Category, CategoryTranslation, ParamTranslation, Product, ProductAdditionalProduct, ProductFile, ProductFileTranslation, ProductImage, ProductImageTranslation, Price, ProductPrice, SearchProduct, ProductTranslation, ProductVideo
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
     * Event is triggered after moderator accepting partner's product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_ACCEPT_PRODUCT = 'afterAcceptProduct';

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
                            'up', 'down', 'generate-seo-url',
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

        $selectedLanguage = (!empty($languageId)) ? Language::findOne($languageId) :  Language::getCurrent();

        if (!empty($id)) {
            $product = Product::findOne($id);

            if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
                $products_translation = ProductTranslation::find()->where([
                    'product_id' => $id,
                    'language_id' => $languageId
                ])->one() ?? new ProductTranslation();
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

                        $this->trigger(self::EVENT_AFTER_ACCEPT_PRODUCT, new ProductEvent([
                            'id' => $id
                        ]));

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