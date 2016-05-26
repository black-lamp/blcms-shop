<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use Yii;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public function actionIndex() {
        return $this->render('index', [
            'categories' => Category::find()->with(['translations'])->all(),
            'products' => Product::find()->with(['translations'])->all(),
        ]);
    }

    public function actionShow($id = null) {
        $product = Product::findOne($id);
        return $this->render('show', [
            'categories' => Category::find()->with(['translations'])->all(),
            'product' => $product,
            'params' => Param::find()->where([
                'product_id' => $id
                ])->all(),
        ]);
    }

    public function actionAddToCart($product_id, $price, $count, $tara) {
        $cart = Yii::$app->session->get('cart', []);

        if(!empty($cart)){
            foreach($cart as &$product) {
                if($product['id'] == $product_id && $product['tara'] == $tara) {
                    $product['count'] += $count;
                    $product['price'] += $price;
                    Yii::$app->session->set('cart', $cart);
                    return $this->redirect(['/shop/product/' . $product_id]);
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
        return $this->redirect(['/shop/product/' . $product_id]);
    }

    public function actionCart() {

        $this->layout = 'inside';

        $session = Yii::$app->session;
        $products = $session->get('cart', []);

        $client = new Clients();

        $total_sum = 0;
        if(!empty($products)) {
            foreach ($products as $key => $pr) {
                $total_sum += $pr['price'];
                $item = Product::find()->where(['id' => $pr['id']])->with('image')->one();
                $products[$key]['title'] = $item->title;
                $products[$key]['image'] = $item->image->url;
            }
        }

        $post = Yii::$app->request->post();
        if($post) {

            if($client->load($post)) {
                if($client->validate()) {
                    if($client->save()) {

                        Yii::$app->mailer->compose()
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo(Yii::$app->params['adminEmail'])
                            ->setSubject('Новый заказ Pools Gallery')
                            ->setHtmlBody('
                                <h4>Список товаров:</h4>

                                <p><strong>Кол-во товаров:</strong> '.count($products).'</p>
                                <p><strong>Общая сумма заказа:</strong> '.$total_sum.' грн.</p>

                                <h4>Данные клиента:</h4>
                                <p><strong>Имя:</strong> ' . $client->name . '</p>
                                <p><strong>Email:</strong> ' . $client->email . '</p>
                                <p><strong>Телефон:</strong> ' . $client->phone . '</p>
                                ')
                            ->send();

                        Yii::$app->mailer->compose()
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo($client->email)
                            ->setSubject('Интернет-магазин Pools Gallery')
                            ->setHtmlBody('
                                <h4>Ваш заказ принят.</h4>
                                <p><strong>Кол-во товаров:</strong> '.count($products).'</p>
                                <p><strong>Общая сумма заказа:</strong> '.$total_sum.' грн.</p>
                                <p>Наши менеджеры свяжуться с Вами в ближайшее время</p>
                                ')
                            ->send();

                        $session->set('cart', '');
                        $session->set('client_id', $client->id);

                        return $this->redirect(['/shop/order-success']);
                    }
                }
            }
        }

        return $this->render('cart', [
            'products' => $products,
            'order' => $client
        ]);
    }

    public function actionCartClear() {
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