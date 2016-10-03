<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var dektrium\user\models\User $user
 */

use yii\helpers\Html;
use yii\widgets\Menu;


$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= $user->profile->name ? $user->profile->name . ' ' . $user->profile->surname : $user->username ?>
        </h3>
    </div>
    <div class="panel-body">
        <?= Menu::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked',
            ],
            'items' => [
                ['label' => Yii::t('shop', 'Profile'), 'url' => ['/user/settings/profile']],
                ['label' => Yii::t('shop', 'Addresses'), 'url' => ['/user/settings/addresses']],
                ['label' => Yii::t('shop', 'Account'), 'url' => ['/user/settings/account']],
                [
                    'label' => Yii::t('user', 'Networks'),
                    'url' => ['/user/settings/networks'],
                    'visible' => $networksVisible
                ],
            ],
        ]) ?>
    </div>
</div>
