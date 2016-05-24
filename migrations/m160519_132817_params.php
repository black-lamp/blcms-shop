<?php

use yii\db\Migration;

class m160519_132817_params extends Migration
{
    public function safeUp()
    {
        $this->createTable('shop_params', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
        ]);
        $this->createTable('shop_params_translation', [
            'id' => $this->primaryKey(),
            'params_id' => $this->integer(),
            'language_id' => $this->integer(),
            'name' => $this->string(),
            'value' => $this->string(),
        ]);
        $this->addForeignKey('params_params_translation', 'shop_params_translation', 'params_id', 'shop_params', 'id', 'cascade', 'cascade');
        $this->addForeignKey('params_language', 'shop_params_translation', 'language_id', 'language', 'id', 'cascade', 'cascade');
        $this->addForeignKey('params_product', 'shop_params_translation', 'params_id', 'shop_product', 'id', 'cascade', 'cascade');

    }

    public function safeDown()
    {
        $this->dropForeignKey('params_params_translation', 'shop_params_translation');
        $this->dropForeignKey('params_language', 'shop_params_translation');

        $this->dropTable('shop_params');
        $this->dropTable('shop_params_translation');
    }
}
