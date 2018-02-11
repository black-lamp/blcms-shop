<?php

use yii\db\Migration;

class m180218_125128_add_column_product_code extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_product', 'code', $this->string());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_product', 'code');
        return true;
    }
}