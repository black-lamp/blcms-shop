<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamsTranslation;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\ProductSearch;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ProductVideo;
use bl\multilang\entities\Language;
use common\models\User;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public $tabTitle;
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => new ProductSearch,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => Product::find()
                ->with(['category'])
                ->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC])
                ->all(),
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionSave($languageId = null, $productId = null)
    {
        if (!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        } else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($productId)) {
            $product = Product::findOne($productId);
            $products_translation = ProductTranslation::find()->where([
                'product_id' => $productId,
                'language_id' => $languageId
            ])->one();
            if (empty($products_translation))
                $products_translation = new ProductTranslation();
        } else {
            $product = new Product();
            $products_translation = new ProductTranslation();

            $product->owner = Yii::$app->user->id;
        }

        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

        return $this->render('save', [
            'viewName' => 'add-basic',
            'selectedLanguage' => $selectedLanguage,
            'product' => $product,

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

    public function actionRemove($id)
    {
        Product::deleteAll(['id' => $id]);
        return $this->actionIndex();
    }

    public function actionAddBasic($languageId = null, $productId = null) {
        if (!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        } else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($productId)) {
            $product = Product::findOne($productId);
            $products_translation = ProductTranslation::find()->where([
                'product_id' => $productId,
                'language_id' => $languageId
            ])->one();
            if (empty($products_translation))
                $products_translation = new ProductTranslation();
        } else {
            $product = new Product();
            $products_translation = new ProductTranslation();

            $product->owner = Yii::$app->user->id;
        }

        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

        if (Yii::$app->request->isPost) {

            $product->load(Yii::$app->request->post());
            $products_translation->load(Yii::$app->request->post());

            if ($product->validate() && $products_translation->validate()) {
                $product->save();
                $products_translation->product_id = $product->id;
                $products_translation->language_id = $selectedLanguage->id;
                $products_translation->save();

                return $this->redirect(Url::toRoute(['save',
                    'productId' => $product->id,
                    'languageId' =>$selectedLanguage->id
                ]));
            }
        }

        return $this->render('save', [
            'viewName' => 'add-basic',
            'selectedLanguage' => $selectedLanguage,
            'product' => $product,

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

    public function actionAddParam($languageId = null, $productId = null)
    {
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

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        )
        {
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

            'params' => [
                'product' => Product::findOne($productId),
                'param' => new Param(),
                'param_translation' => new ParamTranslation(),
                'selectedLanguage' => Language::findOne($languageId),
                'products' => Product::find()->with('translations')->all(),
                'productId' => $productId
            ]
        ]);

    }

    public function actionDeleteParam($id)
    {
        Param::deleteAll(['id' => $id]);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUp($id)
    {
        if (!empty($product = Product::findOne($id))) {
            $product->movePrev();
        }
        return $this->actionIndex();
    }

    public function actionDown($id)
    {
        if ($product = Product::findOne($id)) {
            $product->moveNext();
        }
        return $this->actionIndex();
    }


    public function actionAddImage($productId, $languageId) {
        $product = Product::findOne($productId);
        $image_form = new ProductImageForm();
        $image = new ProductImage();

        if (Yii::$app->request->isPost) {

            $image_form->load(Yii::$app->request->post());
            $image_form->image = UploadedFile::getInstance($image_form, 'image');

            if (!empty($image_form->image) || !empty($image_form->link)) {
                if (!empty($image_form->image)) {
                    $UploadedImageName = $image_form->upload();
                    $image->file_name = $UploadedImageName;
                    $image->alt = $image_form->alt;
                    $image->product_id = $product->id;
                    if ($image->validate()) {
                        $image->save();
                    }
                }
                if (!empty($image_form->link)) {
                    $image_name = $image_form->copy($image_form->link);
                    $image->file_name = $image_name;
                    $image->alt = $image_form->alt;
                    $image->product_id = $product->id;
                    if ($image->validate()) {
                        $image->save();
                    }
                }
            }
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        )
        {
            return $this->renderPartial('add-image', [
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'image_form' => new ProductImageForm()
            ]);
        }
        return $this->render('save', [
            'viewName' => 'add-image',
            'selectedLanguage' => Language::findOne($languageId),
            'product' => $product,

            'params' => [
                'selectedLanguage' => Language::findOne($languageId),
                'product' => $product,
                'image_form' => new ProductImageForm()
            ]
        ]);
    }

    public function actionDeleteImage($id)
    {
        $dir = Yii::getAlias('@frontend/web/images');

        if (!empty($id)) {
            $image = ProductImage::findOne($id);
            ProductImage::deleteAll(['id' => $id]);

            unlink($dir . '/shop-product/' . $image->file_name . '-big.jpg');
            unlink($dir . '/shop-product/' . $image->file_name . '-small.jpg');
            unlink($dir . '/shop-product/' . $image->file_name . '-thumb.jpg');

            return $this->redirect(Yii::$app->request->referrer);

        }
        return false;
    }

    public function actionAddVideo($productId, $languageId)
    {
        $product = Product::findOne($productId);
        $video = new ProductVideo();
        $videoForm = new ProductVideoForm();


        if (Yii::$app->request->isPost) {

            $video->load(Yii::$app->request->post());

//            if (!empty($video->resource) && !empty($videoForm->file_name)) {

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
                    }
                    else {
                        \Yii::$app->session->setFlash('error', \Yii::t('shop', 'Sorry, this format is not supported'));
                    }
                }
                elseif ($video->resource == 'vimeo') {
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
                    }
                    else {
                        \Yii::$app->session->setFlash('error', \Yii::t('shop', 'Sorry, this format is not supported'));
                    }
                }
//            }
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        )
        {
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

            'params' => [
                'product' => $product,
                'selectedLanguage' => Language::findOne($languageId),
                'video_form' => new ProductVideo(),
                'video_form_upload' => new ProductVideoForm(),
                'videos' => ProductVideo::find()->where(['product_id' => $product->id])->all()
            ]
        ]);
    }

    public function actionDeleteVideo($id)
    {
        $dir = Yii::getAlias('@frontend/web/video');

        if (!empty($id)) {
            $video = ProductVideo::findOne($id);
            if ($video->resource == 'videofile') {
                unlink($dir . '/' . $video->file_name);
            }
            ProductVideo::deleteAll(['id' => $id]);

            return $this->redirect(Yii::$app->request->referrer);
        }
        return false;
    }

    public function actionAddPrice($productId, $languageId) {
        $price = new ProductPrice();
        $priceTranslation = new ProductPriceTranslation();

        $product = Product::findOne($productId);
        $selectedLanguage = Language::findOne($languageId);

        if(\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if($price->load($post) && $priceTranslation->load($post)) {
                $price->product_id = $product->id;
                if($price->save()) {
                    $priceTranslation->price_id = $price->id;
                    $priceTranslation->language_id = $selectedLanguage->id;
                    if($priceTranslation->save()) {
                        $price = new ProductPrice();
                        $priceTranslation = new ProductPriceTranslation();
                    }
                }
            }
        }
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        )
        {
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

            'params' => [
                'priceList' => $product->prices,
                'priceModel' => $price,
                'priceTranslationModel' => $priceTranslation,
                'product' => $product,
                'languages' => Language::findAll(['active' => true]),
                'language' => $selectedLanguage
            ]
        ]);
    }

    public function actionRemovePrice($priceId, $productId, $languageId) {
        ProductPrice::deleteAll(['id' => $priceId]);
        return $this->actionAddPrice($productId, $languageId);
    }
}
