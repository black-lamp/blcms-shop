<?php

use yii\db\Migration;

class m170921_125128_add_column_category_created_at extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_category', 'created_at', $this->dateTime());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_category', 'created_at');
        return true;
    }
}
