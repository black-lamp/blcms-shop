<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.03.2016
 * Time: 15:58
 */
namespace bl\cms\shop\common\entities;
use yii\db\ActiveRecord;
class Clients extends ActiveRecord
{
    public function rules() {
        return [
            ['email', 'required'],
            ['email', 'email'],
            [['name', 'phone'], 'required'],
        ];
    }
    public static function tableName() {
        return 'clients';
    }
}