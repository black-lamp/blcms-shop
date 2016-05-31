<?php
/**
 * @author Albert Gainutdinov
 */

namespace bl\cms\shop\frontend\controllers;


use bl\cms\shop\common\entities\Product;
use common\entities\Clients;
use Yii;
use yii\web\Controller;

class CartController extends Controller
{
    public function actionAddToCart($product_id, $price, $count, $tara) {
        $cart = Yii::$app->session->get('cart', []);

        if(!empty($cart)){
            foreach($cart as &$product) {
                if($product['id'] == $product_id && $product['tara'] == $tara) {
                    $product['count'] += $count;
                    $product['price'] += $price;
                    Yii::$app->session->set('cart', $cart);
                    return $this->redirect(['/shop/product/show?id=' . $product_id]);
                }
            }
        }

        $cart[] = [
            'id' =>$product_id,
            'count' => $count,
            'price' => $price,
            'tara' => $tara,
        ];
        Yii::$app->session->set('cart', $cart);
        return $this->redirect(['/shop/product/show?id=' . $product_id]);
    }

    public function actionIndex() {

        $this->layout = 'inside';

        $session = Yii::$app->session;
        $addedProducts = $session->get('cart', []);

        $client = new Clients();

        $total_sum = 0;
        if(!empty($addedProducts)) {
            $productsList = '<ul>';
            foreach ($addedProducts as $key => $addedProduct) {
                $productsList .= '<li>' . $addedProduct['title'] . '</li>';
                $total_sum += $addedProduct['price'];
                $product = Product::find()->where(['id' => $addedProduct['id']])->one();
                $addedProducts[$key]['title'] = $product->translation->title;
                $addedProducts[$key]['imageFile'] = $product->image_name;
            }
            $productsList = '<ul>';
        }
        $post = Yii::$app->request->post();
        if($post) {

            if($client->load($post)) {
                if($client->validate()) {
                    if($client->save()) {
                        Yii::$app->mailer->compose('@frontend/themes/pools-gallery/modules/blcms-shop/frontend/views/cart/mail/admin',
                            ['addedProducts' => $addedProducts, 'total_sum' => $total_sum, 'client' => $client])
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo(Yii::$app->params['adminEmail'])
                            ->setSubject('Новый заказ Pools Gallery')
                            ->send();

                        Yii::$app->mailer->compose('@frontend/themes/pools-gallery/modules/blcms-shop/frontend/views/cart/mail/client',
                            ['addedProducts' => $addedProducts, 'total_sum' => $total_sum])
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo($client->email)
                            ->setSubject('Интернет-магазин Pools Gallery')
                            ->send();

                        $session->set('cart', '');
                        $session->set('client_id', $client->id);

                        return $this->redirect(['/shop/cart/order-success']);
                    }
                }
            }
        }

        return $this->render('index', [
            'products' => $addedProducts,
            'order' => $client,
            'totalSum' => $total_sum
        ]);
    }

    public function actionClear() {
        Yii::$app->session->set('cart', '');
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