<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class FaqIdentifyARModel
 * @property integer $question_id
 * @property integer $user_id
 * @package bengbeng\framework\models\cms
 */
class FaqIdentifyARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_faq_identify}}';
    }
}