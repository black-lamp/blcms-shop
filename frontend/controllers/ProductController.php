<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ProductPrice;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public function actionShow($id = null) {
        $product = Product::findOne($id);

        if(empty($product)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        /*Getting SEO data*/
        $this->view->title = $product->translation->seoTitle;
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => html_entity_decode($product->translation->seoDescription)
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => html_entity_decode($product->translation->seoKeywords)
        ]);

        return $this->render('show', [
            'categories' => Category::find()->with(['translations'])->all(),
            'country' => ProductCountry::find()
                ->with(['translations'])
                ->where(['id' => $product->country_id])
                ->one(),
            'product' => $product,
            'params' => Param::find()->where([
                'product_id' => $id
                ])->all(),
        ]);
    }

    public function actionXml() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=UTF-8');

        return $this->renderPartial('xml', [
            'categories' => Category::find()->all(),
            'products' => Product::findAll(['export' => true])
        ]);
    }
}