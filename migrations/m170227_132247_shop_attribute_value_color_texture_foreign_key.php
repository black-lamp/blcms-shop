<?php

use yii\db\Migration;

class m170227_132247_shop_attribute_value_color_texture_foreign_key extends Migration
{
    public function up()
    {
        $this->addColumn('shop_attribute_value_color_texture', 'value_id', $this->integer());
        $this->addForeignKey(
            'shop_attribute_value_color_texture_value',
            'shop_attribute_value_color_texture', 'value_id', 'shop_attribute_value', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropForeignKey('shop_attribute_value_color_texture_value',
            'shop_attribute_value_color_texture');
        $this->dropColumn('shop_attribute_value_color_texture', 'value_id');
    }

}
