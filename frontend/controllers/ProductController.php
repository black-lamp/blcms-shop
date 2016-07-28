<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductTranslation;
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
            'category' => Category::find()->where(['id' => $product->category_id])->one(),
            'params' => Param::find()->where([
                'product_id' => $id
                ])->all(),
            'recommendedProducts' => $this->recommendedProducts($id, $product->category_id)
        ]);
    }

    public function actionXml() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=UTF-8');

        return $this->renderPartial('xml', [
            'categories' => Category::find()->all(),
            'products' => Product::findAll(['export' => true]),
            'date' => ProductTranslation::find()->orderBy(['update_time' => SORT_DESC])->one()->update_time
        ]);
    }
    public function actionHlxml() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=UTF-8');

        return $this->renderPartial('hlxml', [
            'categories' => Category::find()->all(),
            'products' => Product::findAll(['export' => true]),
            'date' => ProductTranslation::find()->orderBy(['update_time' => SORT_DESC])->one()->update_time
        ]);
    }

    /**
     * This method return array of two previous and two next in order of products
     */
    private function recommendedProducts($id, $categoryId) {
        $previous = Product::find()->with('translations')->where(['<', 'id', $id])->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->limit('2')->all();
        $next = Product::find()->with('translations')->where(['>', 'id', $id])->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->limit('2')->all();

        if (empty($next[1]) && !empty($next[0])) {
            $next[1] = Product::find()->with('translations')->where(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->one();
        }
        if (empty($next[0])) {
            $next = Product::find()->with('translations')->where(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->limit('2')->all();
        }

        if (empty($previous[1]) && !empty($previous[0])) {
            $previous[1] = Product::find()->with('translations')->where(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->one();
        }
        if (empty($previous[0])) {
            $previous = Product::find()->with('translations')->where(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->limit('2')->all();
        }

        $recommendedProducts[0] = $previous[1];
        $recommendedProducts[1] = $previous[0];
        $recommendedProducts[2] = $next[0];
        $recommendedProducts[3] = $next[1];
        return $recommendedProducts;
    }
}