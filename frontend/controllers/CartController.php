<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\SearchOrderProduct;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\frontend\models\AddToCartModel;
use bl\cms\shop\frontend\models\Cart;
use bl\cms\shop\common\entities\Clients;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CartController extends Controller
{

    public function actionAdd()
    {
        $postData = Yii::$app->request->post();

        Yii::$app->cart->add($postData['product_id'], $postData['OrderProduct']['count']);
        \Yii::$app->getSession()->setFlash('success', 'You have successfully added this product to cart');
        return $this->redirect(Yii::$app->request->referrer);
//        return json_encode([
//            'success' => Yii::$app->cart->add($postData['product_id'], $postData['OrderProduct']['count']),
//        ]);
    }

    public function actionShow()
    {
        $cart = \Yii::$app->cart;
        $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => $cart::STATUS_INCOMPLETE])->one();
        if (!empty($order)) {
            $searchModel = new SearchOrderProduct();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $cart::STATUS_INCOMPLETE);

            return $this->render('show', [
                'order' => new Order(),
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else Yii::$app->session->setFlash('error', \Yii::t('shop', 'You did not add to cart no one product.'));
        return $this->render('show');

    }

    public function actionRemove($id) {
        \Yii::$app->cart->removeItem($id);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionMakeOrder()
    {
        $post = Yii::$app->request->post();
        $order = \Yii::$app->cart->makeOrder($post);
        if ($order) {
            \Yii::$app->session->setFlash('success', \Yii::t('shop', 'Your order is accepted. Thank you.'));
        }
        else \Yii::$app->getSession()->setFlash('error', 'Unknown error');
        return $this->render('show');
    }

    /* TODO: remove next actions */
    public function actionIndex() {
        $cart = new Cart();
        $cart->load(Yii::$app->session->get('cart'));

        $client = new Clients();
        $post = Yii::$app->request->post();
        if($post) {
            if(!Clients::findOne(['email' => $post['email']])) {
                if ($client->load($post)) {
                    if ($client->validate()) {
                        if ($client->save()) {
                        }
                    }
                }
            }
            try {
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
            catch(Exception $ex) {
                return $this->render('index', [
                    'cart' => $cart,
                    'errors' => [
                        Yii::t('frontend/shop/order', 'При оформлении заказа возникла ошибка. Просим прощения за неудобства.')
                    ]
                ]);
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