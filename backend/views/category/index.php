<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\shop\common\entities\SearchCategory
 * @var $dataProvider yii\data\ActiveDataProvider
 */

//use bl\cms\itpl\shop\backend\assets\CategoriesIndexAsset;
use bl\cms\shop\common\entities\Category;
use bl\multilang\entities\Language;
use yii\helpers\Html;

$this->title = Yii::t('shop', 'Categories');
$this->params['breadcrumbs'] = [
    Yii::t('shop', 'Shop'),
    $this->title
];
//CategoriesIndexAsset::register($this);
?>

<div class="ibox">

    <div class="ibox-title">
        <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-user-plus']) .
            Yii::t('shop', 'Create category'), ['save', 'languageId' => Language::getCurrent()->id], ['class' => 'btn btn-primary btn-xs pull-right']);
        ?>
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= Html::encode($this->title); ?>
        </h5>
    </div>

    <div class="ibox-content">
        <?= \bl\cms\shop\widgets\TreeWidget::widget([
            'className' => Category::className(),
            'isGrid' => true,
            'appName' => '/admin'
        ]); ?>

        <div class="row">
            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-user-plus']) .
                Yii::t('shop', 'Create category'), ['save', 'languageId' => Language::getCurrent()->id],
                ['class' => 'btn btn-primary btn-xs pull-right']);
            ?>
        </div>

    </div>
</div>