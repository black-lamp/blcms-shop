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

        $product = Product::findOne($id);

        if (!empty($product)) {

            $this->setSeoData($product);

            $this->trigger(self::EVENT_AFTER_SHOW);

            return $this->render('show', [
                'product' => $product,
                'cart' => new CartForm(),
                'defaultCombination' => Combination::find()->where([
                    'product_id' => $id,
                    'default_combination' => true
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
            'products' => Product::findAll(['export' => true]),
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
            'products' => Product::findAll(['export' => true]),
            'date' => ProductTranslation::find()->orderBy(['update_time' => SORT_DESC])->one()->update_time
        ]);
    }

    /**
     * Sets page title, meta-description and meta-keywords.
     * @param $model
     */
    private function setSeoData($model)
    {
        $this->view->title = !empty(($model->translation->seoTitle)) ?
            strip_tags($model->translation->seoTitle) : strip_tags($model->translation->title);
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => strip_tags($model->translation->seoDescription) ?? ''
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => strip_tags($model->translation->seoKeywords) ?? ''
        ]);
    }
}