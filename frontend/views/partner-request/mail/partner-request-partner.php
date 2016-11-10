<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $partnerRequest array
 */
?>

<h1>
    <?= Yii::t('shop', 'Partner request'); ?>
</h1>

<p>
    <?= Yii::t('shop', 'Your request was received and is awaiting processing.'); ?>
</p>

<hr>

<p>
    <b><?= Yii::t('shop', 'Company name'); ?></b>: <?= $partnerRequest['company_name']; ?>
</p>
<p>
    <b><?= Yii::t('shop', 'Website'); ?></b>: <?= $partnerRequest['website']; ?>
</p>
<p>
    <b><?= Yii::t('shop', 'Message'); ?></b>: <?= $partnerRequest['message']; ?>
</p>