<?php
namespace bl\cms\shop\common\entities;
use bl\cms\shop\common\components\user\models\UserGroup;
use Exception;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "shop_combination_price".
 *
 * @property integer $id
 * @property integer $combination_id
 * @property integer $price_id
 *
 * @property Price $priceForAllUsers
 * @property Price $priceForCurrentUserGroup
 * @property Price $priceByUserGroup
 * @property Combination $combination
 */
class CombinationPrice extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_combination_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['combination_id', 'price_id'], 'integer'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['combination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Combination::className(), 'targetAttribute' => ['combination_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('shop', 'ID'),
            'combination_id' => \Yii::t('shop', 'Combination'),
            'price_id' => \Yii::t('shop', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceForAllUsers()
    {
        $price = Price::find()
            ->where(['id' => $this->price_id, 'user_group_id' => UserGroup::USER_GROUP_ALL_USERS]);
        return $price;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceForCurrentUserGroup()
    {
        $price = Price::find()
            ->where(['id' => $this->price_id, 'user_group_id' => \Yii::$app->user->identity->user_group_id]);
        return $price;
    }

    /**
     * @param int $userGroupId
     * @return array|\yii\db\ActiveQuery
     * @throws Exception
     */
    public function getPriceByUserGroup(int $userGroupId) {
        if (!empty($userGroupId)) {
            $price = Price::find()
                ->where(['id' => $this->price_id, 'user_group_id' =>$userGroupId])->one();
            return $price;
        }
        else throw new Exception('User group id is empty');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombination()
    {
        return $this->hasOne(Combination::className(), ['id' => 'combination_id']);
    }
}
