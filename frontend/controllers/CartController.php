<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\frontend\models\AddToCartModel;
use bl\cms\shop\frontend\models\Cart;
use common\entities\Clients;
use Yii;
use yii\web\Controller;

class CartController extends Controller
{
    public function actionAddToCart() {
        if (Yii::$app->request->isPost) {
            $model = new AddToCartModel();
            if($model->load(Yii::$app->request->post())) {
                if($model->validate()) {
                    if($model->add()) {
                    }
                }
            }
            return $this->goBack(['shop/category/show']);
        }
    }

    public function actionIndex() {

        $cart = new Cart();
        $cart->load(Yii::$app->session->get('cart'));

        $client = new Clients();
        $post = Yii::$app->request->post();
        if($post) {
            if ($client->load($post)) {
                if ($client->validate()) {
                    if ($client->save()) {
                        Yii::$app->mailer->compose('@frontend/themes/pools-gallery/modules/blcms-shop/frontend/views/cart/mail/admin',
                            ['addedProducts' => $cart->items, 'total_sum' => $cart->sum, 'client' => $client])
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo(Yii::$app->params['adminEmail'])
                            ->setSubject('Новый заказ Pools Gallery')
                            ->send();

                        Yii::$app->mailer->compose('@frontend/themes/pools-gallery/modules/blcms-shop/frontend/views/cart/mail/client',
                            ['addedProducts' => $cart->items, 'total_sum' => $cart->sum])
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo($client->email)
                            ->setSubject('Интернет-магазин Pools Gallery')
                            ->send();

                        Yii::$app->session->set('cart', '');
                        Yii::$app->session->set('client_id', $client->id);

                        return $this->redirect(['/shop/cart/order-success']);
                    }
                }
            }
        }
        return $this->render('index', [
            'cart' => $cart
        ]);
    }

    public function actionClear() {
        Yii::$app->session->set('cart', []);
        return $this->redirect(['/shop/cart']);
    }

    public function actionCartDelete($count = null) {
        if(!empty($count)) {
            $session = Yii::$app->session;
            $products = $session->get('cart');
            unset($products[$count - 1]);
            $session->set('cart', $products);
            if(!empty($products)) {
                return $this->redirect(['/shop/cart']);
            }
            else {
                return $this->redirect(['/shop']);
            }
        }
    }

    public function actionOrderSuccess() {
        return $this->render('order-success');
    }
}