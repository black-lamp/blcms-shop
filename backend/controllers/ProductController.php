<?php
namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
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
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov
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
            }
        }

        $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();

        return $this->render('save', [
            'languages' => Language::find()->all(),
            'params_translation' => new ParamTranslation(),
            'product' => $product,
            'products_translation' => $products_translation,
            'categories' => CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(),
            'selectedLanguage' => $selectedLanguage,
            'categoriesTree' => Category::findChilds($categoriesWithoutParent),
        ]);
    }

    public function actionRemove($id) {
        Product::deleteAll(['id' => $id]);
        return $this->actionIndex();
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
            }
            else
                Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
        }

        return $this->renderPartial('add-param', [
            'product' => Product::findOne($productId),
            'param' => new Param(),
            'param_translation' => new ParamTranslation(),
            'selectedLanguage' => Language::findOne($languageId),
            'products' => Product::find()->with('translations')->all(),
            'productId' => $productId
        ]);

    }
  

    public function actionDeleteParam($id, $productId, $languageId) {
        Param::deleteAll(['id' => $id]);
        return $this->renderPartial('add-param', [
            'product' => Product::findOne($productId),
            'param' => new Param(),
            'param_translation' => new ParamTranslation(),
            'selectedLanguage' => Language::findOne($languageId),
            'products' => Product::find()->with('translations')->all(),
            'productId' => $productId
        ]);

    }

    public function actionUp($id) {
        if(!empty($product = Product::findOne($id))) {
            $product->movePrev();
        }

        return $this->actionIndex();
    }

    public function actionDown($id) {
        if($product = Product::findOne($id)) {
            $product->moveNext();
            return $this->actionIndex();
        }

    }

    public function actionAddImage($productId) {
        $product = Product::findOne($productId);
        $image_form = new ProductImageForm();

        if(Yii::$app->request->isPost) {

            $product->load(Yii::$app->request->post());

            $image_form->cover = UploadedFile::getInstance($image_form, 'cover');
            $image_form->thumbnail = UploadedFile::getInstance($image_form, 'thumbnail');
            $image_form->menu_item = UploadedFile::getInstance($image_form, 'menu_item');

            if (!empty($image_form->cover) || !empty($image_form->thumbnail) || !empty($image_form->menu_item)) {
                $image_name = $image_form->upload();
                if (!empty($image_form->cover)) {
                    $product->cover = $image_name['cover'];
                }
                if (!empty($image_form->thumbnail)) {
                    $product->thumbnail = $image_name['thumbnail'];
                }
                if (!empty($image_form->menu_item)) {
                    $product->menu_item = $image_name['menu_item'];
                }
            }

            if($product->validate())
            {
                $product->save();
            }
        }

        return $this->renderPartial('add-image', [
            'product' => $product,
            'image_form' => $image_form
        ]);
    }
}
