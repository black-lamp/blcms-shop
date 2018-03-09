<?php
namespace bl\cms\shop\frontend\controllers;

use Yii;
use bl\cms\cart\models\CartForm;
use yii\web\NotFoundHttpException;
use yii\web\{
    Response, Controller
};
use bl\cms\shop\frontend\traits\EventTrait;
use bl\cms\shop\common\entities\Combination;
use bl\cms\shop\frontend\widgets\traits\ProductPricesTrait;
use bl\cms\shop\common\entities\{
    Category, Product, ProductTranslation
};

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{

    use EventTrait;
    use ProductPricesTrait;

    /**
     * Event is triggered before.
     * Triggered with \bl\cms\shop\frontend\traits\EventTrait.
     */
    const EVENT_BEFORE_SHOW = 'beforeShow';

    /**
     * Event is triggered after creating RegistrationForm class.
     * Triggered with \bl\cms\shop\frontend\traits\EventTrait.
     */
    const EVENT_AFTER_SHOW = 'afterShow';

    /**
     * Shows Product model
     *
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShow(int $id)
    {

        $this->trigger(self::EVENT_BEFORE_SHOW, $this->getViewedProductEvent($id));

        $product = Product::find()
            ->where(['show' => true, 'id' => $id, 'status' => Product::STATUS_SUCCESS])
            ->one();

        if (!empty($product)) {

            $this->setSeoData($product);

            $this->trigger(self::EVENT_AFTER_SHOW);

            return $this->render('show', [
                'product' => $product,
                'cart' => new CartForm(),
                'defaultCombination' => Combination::find()->where([
                    'product_id' => $id,
                    'default' => true
                ])->one()
            ]);
        } else throw new NotFoundHttpException();
    }

    /**
     * @return mixed
     */
    public function actionXml()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=UTF-8');

        return $this->renderPartial('xml', [
            'categories' => Category::find()->all(),
            'products' => Product::findAll(['export' => true, 'show' => true]),
            'date' => ProductTranslation::find()->orderBy(['update_time' => SORT_DESC])->one()->update_time
        ]);
    }

    /**
     * @return mixed
     */
    public function actionHlxml()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=UTF-8');

        return $this->renderPartial('hlxml', [
            'categories' => Category::find()->all(),
            'products' => Product::findAll(['show' => true, 'export' => true]),
            'date' => ProductTranslation::find()->orderBy(['update_time' => SORT_DESC])->one()->update_time
        ]);
    }

    /**
     * Sets page title, meta-description and meta-keywords.
     * @param Product $product
     */
    private function setSeoData($product)
    {
        $view = $this->view;
        $titleTemplateSetting = 'seo-title-template-' . Yii::$app->language;
        $descriptionTemplateSetting = 'seo-description-template-' . Yii::$app->language;

        if(!empty($product->translation->seoTitle)) {
            $title = $product->translation->seoTitle;
        }
        else if (Yii::$app->has('settings')
            && Yii::$app->settings->has('shop-product', $titleTemplateSetting)
            && !empty(Yii::$app->settings->get('shop-product', $titleTemplateSetting))) {
            $titleTemplate = Yii::$app->settings->get('shop-product', $titleTemplateSetting);
            $title = strtr($titleTemplate, [
                '{title}' => $product->translation->title
            ]);
        }
        else {
            $title = $product->translation->title;
        }

        if(!empty($product->translation->seoDescription)) {
            $description = $product->translation->seoDescription;
        }
        else if (Yii::$app->has('settings')
            && Yii::$app->settings->has('shop-product', $descriptionTemplateSetting)
            && !empty(Yii::$app->settings->get('shop-product', $descriptionTemplateSetting))) {
            $descriptionTemplate = Yii::$app->settings->get('shop-product', $descriptionTemplateSetting);
            $description = strtr($descriptionTemplate, [
                '{title}' => $product->translation->title
            ]);
        }
        else {
            $description = '';
        }

        $view->title = strip_tags(html_entity_decode($title));
        $view->registerMetaTag([
            'name' => 'description',
            'content' => strip_tags(html_entity_decode($description)) ?? ''
        ]);
        $view->registerMetaTag([
            'name' => 'keywords',
            'content' => strip_tags(html_entity_decode($product->translation->seoKeywords)) ?? ''
        ]);
    }
}