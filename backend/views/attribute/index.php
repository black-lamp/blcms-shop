<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\shop\common\entities\SearchAttribute
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use bl\multilang\entities\Language;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = Yii::t('shop', 'Attributes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">

    <div class="panel-heading">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('shop', 'Create attribute'), Url::toRoute(['create', 'languageId' => Language::getCurrent()->id]), ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <div class="panel-body">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterRowOptions' => ['class' => 'm-b-sm m-t-sm'],
            'options' => [
                'class' => 'project-list'
            ],
            'tableOptions' => [
                'id' => 'my-grid',
                'class' => 'table table-hover'
            ],
            'summary' => "",

            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'translation.title',
                'type_id',
                'created_at',
                'updated_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>