<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;

use bl\cms\cart\CartComponent;
use bl\cms\cart\models\CartForm;
use bl\cms\cart\models\DeliveryMethod;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderProduct;
use bl\cms\shop\common\components\user\models\Profile;
use bl\cms\shop\common\components\user\models\User;
use bl\cms\shop\common\components\user\models\UserAddress;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\imagable\helpers\FileHelper;
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
            } else die(var_dump($model->errors));
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

        /*EMPTY CART*/
        if (empty($items)) {
            return $this->render('empty-cart');
        }

        /*CART IS NOT EMPTY*/
        else {
            /*FOR GUEST*/
            if (\Yii::$app->user->isGuest) {

                $order = new Order();
                $user = new User();
                $profile = new Profile();
                $address = new UserAddress();

                $products = Product::find()->where(['in', 'id', ArrayHelper::getColumn($items, 'id')])->all();

                foreach ($products as $product) {
                    foreach ($items as $item) {
                        if ($item['id'] == $product->id) {
                            $product->count = $item['count'];
                            $product->price = (!empty($item['priceId'])) ? ProductPrice::findOne($item['priceId'])->salePrice : $product->price;
                        }
                    }
                }

                return $this->render('show-for-guest', [
                    'products' => $products,
                    'order' => $order,
                    'profile' => $profile,
                    'user' => $user,
                    'address' => $address,
                ]);
            }
            /*FOR USER*/
            else {
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
        if (\Yii::$app->user->isGuest) {

            $profile = new Profile();
            $user = new User();
            $order = new Order();
            $address = new UserAddress();


            if ($profile->load(\Yii::$app->request->post()) ||
                $user->load(\Yii::$app->request->post()) ||
                $order->load(\Yii::$app->request->post()) ||
                $address->load(\Yii::$app->request->post())
            ) {
                $this->sendMail($profile, $products = null, $user, $order, $address);
                return $this->render('order-success');
            }


        }
        else {
            $profile = Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();
            $user = $profile->user;
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => CartComponent::STATUS_INCOMPLETE])->one();
            $address = (!empty($order->address)) ? $order->address : '';

            $post = Yii::$app->request->post();
            if(\Yii::$app->cart->makeOrder($post)) {
                $this->sendMail($profile, $products = null, $user, $order, $address);
                \Yii::$app->session->setFlash('success', \Yii::t('shop', 'Your order is accepted. Thank you.'));
            }
            else \Yii::$app->getSession()->setFlash('error', 'Unknown error');
            return $this->render('show');
        }
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
    }

    public function actionOrderSuccess() {
        return $this->render('order-success');
    }

    public function sendMail($profile = null, $products = null, $user = null, $order = null, $address = null)
    {
        try {
            foreach (\Yii::$app->cart->sendTo as $adminMail) {
                Yii::$app->mailer->compose('@vendor/black-lamp/blcms-cart/views/mail/new-order',
                    ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                    ->setFrom(\Yii::$app->cart->sender)
                    ->setTo($adminMail)
                    ->setSubject(Yii::t('cart', 'New order.'))
                    ->send();
            }
            Yii::$app->mailer->compose('@vendor/black-lamp/blcms-cart/views/mail/order-success',
                ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                ->setFrom(\Yii::$app->cart->sender)
                ->setTo($user->email)
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

    public function actionGetAreasFromNp() {
        return $this->getResponse('Address', 'getAreas');
    }

    public function actionGetCitiesFromNp() {
        return $this->getResponse('AddressGeneral', 'getSettlements');
    }

    public function actionGetWarehousesFromNp($cityName) {

//        $cityName = (!empty($cityName)) ? $cityName : $this->defaultCityName;

        $methodProperties = [
            'CityName' => $cityName
        ];

        return $this->getResponse('AddressGeneral', 'getWarehouses', $methodProperties);
    }


    /**
     * This method is used for Nova Poshta widget.
     *
     * @param $modelName
     * @param $calledMethod
     * @param null $methodProperties
     * @return string
     */
    private function getResponse($modelName, $calledMethod, $methodProperties = null)
    {

        $data = [
            'apiKey' => $this->token,
            'modelName' => $modelName,
            'calledMethod' => $calledMethod,
            'language' => $this->language,
            'methodProperties' => $methodProperties
        ];

        $post = json_encode($data);

        $result = file_get_contents($this->url, null, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded;\r\n",
                'content' => $post,
            ]
        ]));

        return $result;
    }
}