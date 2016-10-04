<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

namespace bl\cms\shop\common\components\user\models;

use dektrium\user\models\User as BaseModel;
use bl\cms\shop\common\components\user\models\Profile as OverridedProfile;

class User extends BaseModel
{
    /** @var Profile|null */
    private $_profile;

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            if ($this->_profile == null) {
            }

        }
    }
}