<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;

use bl\cms\cart\models\CartForm;
use bl\cms\cart\models\DeliveryMethod;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderProduct;
use bl\cms\shop\common\components\user\models\Profile;
use bl\cms\shop\common\components\user\models\UserAddress;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\frontend\models\Cart;
use bl\cms\shop\common\entities\Clients;
use bl\imagable\helpers\FileHelper;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
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
            } else throw new \yii\base\Exception($model->errors);
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
                            $product->price = (!empty($item['priceId'])) ? ProductPrice::findOne($item['priceId'])->salePrice : $product->price;
                        }
                    }
                }

                return $this->render('show', [
                    'productsFromSession' => $products,
                ]);
            } else {
                $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => $cart::STATUS_INCOMPLETE])->one();
                if (!empty($order)) {
                    $orderProducts = OrderProduct::find()->where(['order_id' => $order->id])->all();

                    $profile = Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();


                    return $this->render('show', [
                        'order' => new Order(),
                        'profile' => $profile,
                        'user' => \Yii::$app->user->identity,
                        'address' => new UserAddress(),
                        'productsFromDB' => $orderProducts,
                    ]);
                }
            }
        } else {
            return $this->render('show');
        }

    }

    public function actionRemove($id)
    {
        \Yii::$app->cart->removeItem($id);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionClear()
    {
        \Yii::$app->cart->clearCart();
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionMakeOrder()
    {
        $post = Yii::$app->request->post();
        $order = \Yii::$app->cart->makeOrder($post);
        if ($order) {
            $this->sendMail($order->user->profile, $order->orderProducts);
            \Yii::$app->session->setFlash('success', \Yii::t('shop', 'Your order is accepted. Thank you.'));
        } else \Yii::$app->getSession()->setFlash('error', 'Unknown error');
        return $this->render('show');
    }

    public function actionGetDeliveryMethod($id)
    {
        if (\Yii::$app->request->isAjax) {

            $method = DeliveryMethod::find()->asArray()->where(['id' => $id])->with('translations')->one();
            $method['image_name'] = '/images/delivery/' .
                FileHelper::getFullName(
                    \Yii::$app->shop_imagable->get('delivery', 'small', $method['image_name']
                    ));
            return json_encode([
                'method' => $method,
                'field' => '<input type="text" id="useraddress-zipcode" class="form-control" name="UserAddress[zipcode]">'
            ]);
        }
        else throw new BadRequestHttpException();
    }

    public function actionOrderSuccess() {
        return $this->render('order-sucess');
    }

    public function sendMail($profile, $products)
    {
        try {
            foreach (\Yii::$app->cart->sendTo as $adminMail) {
                Yii::$app->mailer->compose('@vendor/black-lamp/blcms-cart/views/mail/new-order',
                    ['products' => $products, 'profile' => $profile])
                    ->setFrom(\Yii::$app->cart->sender)
                    ->setTo($adminMail)
                    ->setSubject(Yii::t('cart', 'New order.'))
                    ->send();
            }

            Yii::$app->mailer->compose('@vendor/black-lamp/blcms-cart/views/mail/order-success',
                ['products' => $products, 'profile' => $profile])
                ->setFrom(\Yii::$app->cart->sender)
                ->setTo($profile->user->email)
                ->setSubject(Yii::t('cart', 'Your order is accepted.'))
                ->send();

            return $this->redirect(['/shop/cart/order-success']);
        } catch (Exception $ex) {
            return $this->render('index', [
                'errors' => [
                    Yii::t('cart', 'При оформлении заказа возникла ошибка. Просим прощения за неудобства.')
                ]
            ]);
        }
    }
}