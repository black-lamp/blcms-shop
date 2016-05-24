<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 20.05.2016
 * Time: 10:56
 */

namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\multilang\entities\Language;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class ProductController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'products' => Product::find()
                ->with(['category'])
                ->all(),
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionSave($languageId = null, $productId = null){

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
            if($product->validate() && $products_translation->validate())
            {
                $product->save();
                $products_translation->product_id = $product->id;
                $products_translation->language_id = $languageId;
                $products_translation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->redirect(Url::toRoute('/multishop/product'));
            }
            else
                Yii::$app->getSession()->setFlash('danger', 'Failed to change the record.');
        }
        return $this->render('save', [
            'product' => $product,
            'products_translation' => $products_translation,
            'category' => Category::find()->with('translations')->all(),
            'selectedLanguage' => Language::findOne($languageId),
            'languages' => Language::findAll(['active' => true])
        ]);
    }

    public function actionRemove($id) {
        Product::deleteAll(['id' => $id]);
        return $this->redirect(Url::to(['/multishop/product']));
    }
}
