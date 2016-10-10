<?php
namespace bl\cms\shop\backend\components\form;
use yii\base\Model;
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class DeliveryForm extends Model
{
    public $text = '';
    public $subject = '';
    public function rules()
    {
        return [
            [['text', 'subject'], 'string']
        ];
    }
}