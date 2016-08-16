<?php
namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamsTranslation;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\multilang\entities\Language;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * @author xalbert.einsteinx
 * Date: 20.05.2016
 * Time: 10:56
 */

class ProductController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'products' => Product::find()
                ->with(['category'])
                ->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC])
                ->all(),
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionSave($languageId = null, $productId = null){
        if(!empty($languageId)) {
            $selectedLanguage = Language::findOne($languageId);
        }
        else {
            $selectedLanguage = Language::getCurrent();
        }

        if (!empty($productId)) {
            $product = Product::findOne($productId);
            $products_translation = ProductTranslation::find()->where([
                'product_id' => $productId,
                'language_id' => $languageId
            ])->one();
            if(empty($products_translation))
                $products_translation = new ProductTranslation();
        } else {
            $product = new Product();
            $products_translation = new ProductTranslation();
        }
        if(Yii::$app->request->isPost) {

            $product->load(Yii::$app->request->post());
            $products_translation->load(Yii::$app->request->post());

            $product->imageFile = UploadedFile::getInstance($product, 'imageFile');
            if($product->validate() && $products_translation->validate())
            {
                if (!empty($product->imageFile)) {
                    try {
                        // save image
                        $fileName = Product::generateImageName($product->imageFile->baseName);
                        $imagine = new Imagine();
                        $imagine->open($product->imageFile->tempName)
                            ->save(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-original.jpg'))
                            ->thumbnail(new Box(1500, 1000), ImageInterface::THUMBNAIL_INSET)
                            ->save(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-big.jpg'))
                            ->thumbnail(new Box(400, 400), ImageInterface::THUMBNAIL_INSET)
                            ->save(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-thumb.jpg'));

                        $product->image_name = $fileName;
                    }
                    catch(\Exception $ex) {
                        $product->addError('image_file', 'File save failed');
                    }
                }

                $product->save();
                $products_translation->product_id = $product->id;
                $products_translation->language_id = $languageId;
                $products_translation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->redirect(Url::toRoute('/shop/product'));
            }
            else
                Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
        }
        return $this->render('save', [
            'product' => $product,
            'products_translation' => $products_translation,
            'category' => Category::find()->with('translations')->all(),
            'selectedLanguage' => $selectedLanguage,
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionRemove($id) {
        Product::deleteAll(['id' => $id]);
        return $this->redirect(Url::to(['/shop/product']));
    }
    
    public function actionAddParam($id = null, $languageId = null, $productId = null) {
        if (!empty($id)) {
            $param = Param::find()->where([
                'id' => $id
            ])->one();
            $param_translation = ParamTranslation::find()->where([
                'language_id' => $languageId,
                'param_id' => $id
            ])->one();
            if(empty($param_translation))
                $param_translation = new ParamTranslation();
        }
        else {
            $param = new Param();
            $param->product_id = $productId;
            $param_translation = new ParamTranslation();
        }

        if(Yii::$app->request->isPost) {
            $param->load(Yii::$app->request->post());
            $param_translation->load(Yii::$app->request->post());
            if($param->validate() && $param_translation->validate())
            {
                $param->save();
                $param_translation->param_id = $param->id;
                $param_translation->language_id = $languageId;
                $param_translation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->redirect(Url::to([
                    'save',
                    'productId' => $param->product_id,
                    'languageId' => $param_translation->language_id])
                );
            }
            else
                Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
        }

        return $this->render('add-param', [
            'param' => $param,
            'param_translation' => $param_translation,
            'languages' => Language::findAll(['active' => true]),
            'selectedLanguage' => Language::findOne($languageId),
            'products' => Product::find()->with('translations')->all(),
            'productId' => $productId
        ]);
    }

    public function actionDeleteParam($id, $productId) {
        Param::deleteAll(['id' => $id]);
        return $this->redirect(Url::to([
            'save',
            'productId' => $productId,
        ]));
    }

    public function actionUp($id) {
        $product = Product::findOne($id);
        if(!empty($product)) {
            $product->movePrev();
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDown($id) {
        $product = Product::findOne($id);
        if($product) {
            $product->moveNext();
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
