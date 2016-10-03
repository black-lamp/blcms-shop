<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;
use bl\cms\cart\models\CartForm;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderProduct;
use bl\cms\shop\common\components\user\models\UserAddress;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\frontend\models\Cart;
use bl\cms\shop\common\entities\Clients;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class CartController extends Controller
{

    public function actionAdd()
    {
        $model = new CartForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                Yii::$app->cart->add($model->productId, $model->count, $model->priceId);
                \Yii::$app->getSession()->setFlash('success', Yii::t('shop', 'You have successfully added this product to cart'));
            }
            else throw new \yii\base\Exception($model->errors);
        }

        return $this->redirect(Yii::$app->request->referrer);
//        return json_encode([
//            'success' => Yii::$app->cart->add($postData['product_id'], $postData['OrderProduct']['count']),
//        ]);
    }

    public function actionShow()
    {

        $cart = \Yii::$app->cart;
        $items = $cart->getOrderItems();

        if (!empty($items)) {
            if (\Yii::$app->user->isGuest) {
                $products = Product::find()->where(['in', 'id', ArrayHelper::getColumn($items, 'id')])->all();

                foreach ($products as $product) {
                    foreach ($items as $item) {
                        if ($item['id'] == $product->id) {
                            $product->count = $item['count'];
                        }
                    }
                }

                return $this->render('show', [
                    'products' => $products,
                ]);
            }
            else {
                $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => $cart::STATUS_INCOMPLETE])->one();
                if (!empty($order)) {
                    $orderProducts = OrderProduct::find()->where(['order_id' => $order->id])->all();

                    $products = Product::find()->where(['in', 'id', ArrayHelper::getColumn($orderProducts, 'product_id')])->all();

                    foreach ($products as $product) {
                        foreach ($orderProducts as $item) {
                            if ($item->product_id == $product->id) {
                                $product->count = $item->count;
                            }
                        }
                    }

                    return $this->render('show', [
                        'order' => new Order(),
                        'address' => new UserAddress(),
                        'products' => $products,
                    ]);
                }
            }
        }

        else {
            Yii::$app->session->setFlash('error', \Yii::t('shop', 'You did not add to cart no one product.'));
            return $this->render('show');
        }

    }

    public function actionRemove($id) {
        \Yii::$app->cart->removeItem($id);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionClear() {
        \Yii::$app->cart->clearCart();
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