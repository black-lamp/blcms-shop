<?php

use yii\db\Migration;

class m180128_125128_add_column_category_key extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_category', 'key', $this->string());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_category', 'key');
        return true;
    }
}
