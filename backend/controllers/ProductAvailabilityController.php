<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\ProductAvailability;
use bl\cms\shop\common\entities\ProductAvailabilityTranslation;
use bl\multilang\entities\Language;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductAvailabilityController extends Controller
{
    public function actionIndex() {

        $availabilities = ProductAvailability::find()->with(['translations'])->all();

        return $this->render('index', [
            'availabilities' => $availabilities
        ]);
    }

    /**
     * Creates a new DeliveryMethod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionSave($id = null, $languageId)
    {
        if (!empty($languageId)) {
            if (!empty($id)) {

                $model = $this->findModel($id);
                if (empty($model)) {
                    throw new NotFoundHttpException;
                }
                $modelTranslation = $this->findModelTranslation($id, $languageId);
                if (empty($modelTranslation)) {
                    $modelTranslation = new ProductAvailabilityTranslation();
                }
            }
            else {
                $model = new ProductAvailability();
                $modelTranslation = new ProductAvailabilityTranslation();
            }
        }
        else throw new BadRequestHttpException();


        if ($modelTranslation->load(Yii::$app->request->post())) {

            $model->save(false);

            $modelTranslation->availability_id = $model->id;
            $modelTranslation->language_id = $languageId;
            if ($modelTranslation->validate()) {

                $modelTranslation->save();
                return $this->redirect(['save', 'id' => $model->id, 'languageId' => $languageId]);
            }
        }
        return $this->render('save', [
            'model' => $model,
            'modelTranslation' => $modelTranslation,
            'languages' => Language::find()->all(),
            'selectedLanguage' => Language::findOne($languageId)
        ]);

    }


    /**
     * Deletes an existing DeliveryMethod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DeliveryMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductAvailability the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductAvailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Finds the DeliveryMethodTranslation model based on delivery method id and language id.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelTranslation($id, $languageId)
    {
        $model = ProductAvailabilityTranslation::find()->where([
            'availability_id' => $id,
            'language_id' => $languageId
        ])->one();

        if ($model !== null) {
            return $model;
        } else {
            return false;
        }
    }
}