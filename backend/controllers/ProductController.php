<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\common\entities\Category;
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
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->user->can('viewCompleteProductList')) {
            $searchModel = new SearchProduct();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $notModeratedProductsCount = count(Product::find()->where(['status' => Product::STATUS_ON_MODERATION])->all());

            return $this->render('index', [
                'notModeratedProductsCount' => $notModeratedProductsCount,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'languages' => Language::findAll(['active' => true])
            ]);
        } else throw new ForbiddenHttpException();
    }

    public function actionSave($id = null, $languageId = null)
    {
        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

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
                'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                'params_translation' => new ParamTranslation(),
            ]
        ]);
    }

    public function actionDelete($id)
    {
        if (\Yii::$app->user->can('deleteProduct', ['productOwner' => Product::findOne($id)->owner])) {
            Product::deleteAll(['id' => $id]);
            return $this->redirect('index');
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to delete this product.'));
    }

    public function actionAddBasic($languageId = null, $productId = null)
    {
        if (!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        } else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($productId)) {
            $product = Product::findOne($productId);

            if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
                $products_translation = ProductTranslation::find()->where([
                    'product_id' => $productId,
                    'language_id' => $languageId
                ])->one();
                if (empty($products_translation)) {
                    $products_translation = new ProductTranslation();
                }
            } else throw new ForbiddenHttpException();

        } else {
            if (\Yii::$app->user->can('createProduct')) {
                $product = new Product();
                $products_translation = new ProductTranslation();
            } else throw new ForbiddenHttpException();
        }

        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

        if (Yii::$app->request->isPost) {
            $product->owner = Yii::$app->user->id;
            if (\Yii::$app->user->can('createProductWithoutModeration')) {
                $product->status = Product::STATUS_SUCCESS;
            }

            $product->load(Yii::$app->request->post());
            $products_translation->load(Yii::$app->request->post());

            if ($product->validate() && $products_translation->validate()) {

                if (empty($products_translation->seoUrl)) {
                    $products_translation->seoUrl = Inflector::slug($products_translation->title);
                }

                $product->save();
                $products_translation->product_id = $product->id;
                $products_translation->language_id = $selectedLanguage->id;
                $products_translation->save();
            }
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('add-basic', [
                'languages' => Language::find()->all(),
                'selectedLanguage' => $selectedLanguage,
                'product' => $product,
                'products_translation' => $products_translation,
                'categories' => CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(),
                'categoriesTree' => Category::findChilds($categoriesWithoutParent),
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
                    'categoriesTree' => Category::findChilds($categoriesWithoutParent),
                    'params_translation' => new ParamTranslation(),
                ]
            ]);
        }
    }

    public function actionAddParam($languageId = null, $productId = null)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            $param = new Param();
            $param->product_id = $productId;
            $param_translation = new ParamTranslation();

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

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-param', [
                    'product' => Product::findOne($productId),
                    'param' => new Param(),
                    'param_translation' => new ParamTranslation(),
                    'selectedLanguage' => Language::findOne($languageId),
                    'products' => Product::find()->with('translations')->all(),
                    'productId' => $productId
                ]);
            }
            return $this->render('save', [
                'viewName' => 'add-param',
                'selectedLanguage' => Language::findOne($languageId),
                'product' => Product::findOne($productId),
                'languages' => Language::find()->all(),

                'params' => [
                    'product' => Product::findOne($productId),
                    'param' => new Param(),
                    'param_translation' => new ParamTranslation(),
                    'selectedLanguage' => Language::findOne($languageId),
                    'products' => Product::find()->with('translations')->all(),
                    'productId' => $productId
                ]
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    public function actionDeleteParam($id, $languageId)
    {
        $param = Param::findOne($id);
        $product = Product::findOne($param->product_id);

        if (\Yii::$app->user->can('updateProduct', ['productOwner' => $product->owner])) {
            Param::deleteAll(['id' => $id]);
            return $this->renderPartial('add-param', [
                'product' => $product,
                'param' => new Param(),
                'param_translation' => new ParamTranslation(),
                'selectedLanguage' => Language::findOne($languageId),
                'products' => Product::find()->with('translations')->all(),
                'productId' => $product->id
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

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

    public function actionAddImage($productId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            $product = Product::findOne($productId);
            $image_form = new ProductImageForm();
            $image = new ProductImage();
//die(var_dump(Yii::$app->request->post()));
            if (Yii::$app->request->isPost) {

                $image_form->load(Yii::$app->request->post());
                $image_form->image = UploadedFile::getInstance($image_form, 'image');

                if (!empty($image_form->image)) {
                    $uploadedImage = $image_form->upload();
                    $uploadedImageName = $uploadedImage['imageName'];
                    $uploadedImageExtension = $uploadedImage['imageExtension'];

                    $image->file_name = $uploadedImageName;
                    $image->alt = $image_form->alt2;
                    $image->product_id = $product->id;
                    $image->extension = $uploadedImageExtension;

                    if ($image->validate()) {
                        $image->save();
                    }
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

    public function actionAddVideo($productId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            $product = Product::findOne($productId);
            $video = new ProductVideo();
            $videoForm = new ProductVideoForm();

            if (Yii::$app->request->isPost) {

                $video->load(Yii::$app->request->post());

                $videoForm->load(Yii::$app->request->post());
                $videoForm->file_name = UploadedFile::getInstance($videoForm, 'file_name');
                if ($fileName = $videoForm->upload()) {
                    $video->file_name = $fileName;
                    $video->resource = 'videofile';
                    $video->product_id = $productId;
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

    public function actionAddPrice($productId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            $price = new ProductPrice();
            $priceTranslation = new ProductPriceTranslation();

            $product = Product::findOne($productId);
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

    public function actionRemovePrice($priceId, $productId, $languageId)
    {
        if (\Yii::$app->user->can('updateProduct', ['productOwner' => Product::findOne($productId)->owner])) {
            ProductPrice::deleteAll(['id' => $priceId]);
            return $this->actionAddPrice($productId, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('shop', 'You have not permission to do this action.'));
    }

    public function actionChangeProductStatus($id, $status)
    {
        if (Yii::$app->user->can('moderateProductCreation') && !empty($id) && !empty($status)) {
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

    public function actionGenerateSeoUrl($title)
    {
        return Inflector::slug($title);
    }
}