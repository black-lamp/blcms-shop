<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $partnerRequest array
 * @var $profile array
 */

?>

<h1>
    <?= Yii::t('shop', 'New partner request'); ?>
</h1>

<p>
    <b><?= Yii::t('shop', 'Company name'); ?></b>: <?= $partnerRequest['company_name']; ?>
</p>
<p>
    <b><?= Yii::t('shop', 'Website'); ?></b>: <?= $partnerRequest['website']; ?>
</p>
<p>
    <b><?= Yii::t('shop', 'Message'); ?></b>: <?= $partnerRequest['message']; ?>
</p>

<hr>

<?php if (Yii::$app->user->isGuest) : ?>
    <p>
        <b><?= Yii::t('shop', 'Name'); ?></b>: <?= $profile['name']; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Patronymic'); ?></b>: <?= $profile['patronymic']; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Surname'); ?></b>: <?= $profile['surname']; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Phone'); ?></b>: <?= $profile['phone']; ?>
    </p>
<?php else : ?>
    <p>
        <b><?= Yii::t('shop', 'Name'); ?></b>: <?= Yii::$app->user->profile->name; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Surname'); ?></b>: <?= Yii::$app->user->profile->surname; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Patronymic'); ?></b>: <?= Yii::$app->user->profile->patronomyc; ?>
    </p>
    <p>
        <b><?= Yii::t('shop', 'Phone'); ?></b>: <?= Yii::$app->user->profile->phone; ?>
    </p>
<?php endif; ?>
