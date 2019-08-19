<?php


namespace bengbeng\framework\models\cms;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class AnswersARModel
 * @property integer $answer_id
 * @property integer $question_id 问题ID
 * @property string $content 内容
 * @property string $user_id 用户ID
 * @property integer $status 状态 10正常0删除1违规
 * @property bool $is_comment 是否可以评论
 * @property integer $replytime 回复时间
 * @package bengbeng\framework\models\cms
 */
class AnswersARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_answers}}';
    }
}