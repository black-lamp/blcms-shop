<?php
namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\backend\components\form\DeliveryForm;
use bl\cms\shop\common\entities\Clients;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use bl\cms\shop\backend\components\ClientsModel;
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class ClientsController extends Controller
{
    public function actionList() {
        $clients = Clients::find()->all();
        return $this->render('list', [
            'clients' => $clients
        ]);
    }
    public function actionRemove($id) {
        Clients::deleteAll(['id' => $id]);
        return $this->redirect(['list']);
    }
    public function actionCreate() {
        $model = new Clients();
        if(Yii::$app->request->isPost) {
            if($model->load(Yii::$app->request->post())) {
                if($model->save()) {
                    return $this->redirect(['list']);
                }
            }
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }
    public function actionDelivery() {
        $model = new DeliveryForm();
        $message = '';
        if(\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post())) {
                $clients = Clients::find()->groupBy(['email'])->asArray()->all();
                $mailMessage = Yii::$app->mailer->compose()
                    ->setFrom('info@pools.gallery')
                    ->setTo(ArrayHelper::getColumn($clients, 'email'))
                    ->setSubject($model->subject)
                    ->setHtmlBody($model->text);
                if($mailMessage->send()) {
                    $model = new DeliveryForm();
                    $message = 'Сообщение успешно отправлено!';
                }
            }
        }
        return $this->render('delivery', [
            'model' => $model,
            'message' => $message
        ]);
    }
    public function actionExport() {
        $clients = Clients::find()->all();
        ClientsModel::ClientsToCsv($clients);
    }
}