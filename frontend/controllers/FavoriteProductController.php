<?php
namespace bl\cms\shop\frontend\controllers;

use Yii;
use bl\cms\shop\common\entities\FavoriteProduct;
use bl\cms\shop\common\entities\SearchFavoriteProduct;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * FavoriteProductController implements the CRUD actions for FavoriteProduct model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class FavoriteProductController extends Controller
{

    /**
     * Adds product to list of favorites.
     *
     * @param integer $productId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAdd($productId)
    {
        if (!empty($productId)) {
            if (!Yii::$app->user->isGuest) {
                $model = new FavoriteProduct();

                $model->product_id = $productId;
                $model->user_id = Yii::$app->user->id;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'You have successfully added this product to favorites.');
                }
                else {
                    Yii::$app->session->setFlash('error', 'Error has occurred.');
                }
                return $this->redirect(Yii::$app->request->referrer);
            }
            throw new ForbiddenHttpException();
        }
        else throw new NotFoundHttpException();
    }

    /**
     * Removes product from list of favorites.
     *
     * @param integer $productId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionRemove($productId)
    {
        if (!empty($productId)) {
            if (!Yii::$app->user->isGuest) {
                $model = FavoriteProduct::find()->where(['product_id' => $productId])->one();
                if (!empty($model)) {
                    $model->delete();
                    Yii::$app->session->setFlash('success', 'You have successfully deleted this product from favorites.');
                }
                else {
                    Yii::$app->session->setFlash('error', 'Error has occurred.');
                }
                return $this->redirect(Yii::$app->request->referrer);
            }
            throw new ForbiddenHttpException();
        }
        else throw new NotFoundHttpException();
    }

    /**
     * Lists all FavoriteProduct models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new SearchFavoriteProduct();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}