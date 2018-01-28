<?php

use yii\db\Migration;

class m180127_125128_add_column_category_view extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_category', 'view', $this->string());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_category', 'view');
        return true;
    }
}
