<?php


namespace bengbeng\framework\models\cms;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\AttachmentARModel;
use bengbeng\framework\models\UserARModel;
use yii\db\ActiveQuery;

/**
 * Class AnswersARModel
 * @property integer $answer_id
 * @property integer $question_id 问题ID
 * @property string $content 内容
 * @property string $user_id 用户ID
 * @property integer $status 状态 10正常0删除1违规
 * @property bool $is_comment 是否可以评论
 * @property bool $is_identify 认证用户发布
 * @property integer $replytime 回复时间
 * @package bengbeng\framework\models\cms
 */
class AnswersARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_answers}}';
    }

    public function getImages(){
        return $this->hasMany(AttachmentARModel::className(),['object_id'=>'answer_id'])->where(['att_type' => Enum::MODULE_TYPE_FAQS_REPLAY])->select([
            'object_id', 'obj_url', 'is_default'
        ]);
    }

    public function getUser(){
        return $this->hasOne(UserARModel::className(),['user_id'=>'user_id'])->select([
            'nickname',
            'avatar_head',
            'user_id',
            'unionid',
            'auth_info'
        ]);
    }

    public function getQuestion(){
        return $this->hasOne(QuestionsARModel::className(),['question_id'=>'question_id']);
    }

    public function findAllByQuestionID($question_id = 0){
        return $this->findByAll([
            'question_id' => $question_id,
            'is_identify' => 0,
            'group_id' => 0
        ]);

    }

    public function findAllByQuestionAndUserID($question_id, $user_id){
        return $this->findByAll([
            'question_id' => $question_id,
            'group_id' => $user_id,
//            'is_identify' => 1
        ]);

    }
    public function findGroupAllByUserID($user_id){
        return self::dataSet(function (ActiveQuery $query) use($user_id){

            $query->select(['question_id']);
            $query->with(['question.user']);
            $query->groupBy(['question_id']);
            $query->where([
                'user_id' => $user_id,
            ]);

            $query->orderBy([
                'max(replytime)' => SORT_DESC
            ]);

            $query->asArray();

        });

    }

    private function findByAll($where = false){
        return self::dataSet(function (ActiveQuery $query) use ($where){
            if($this->showField){
                $query->select($this->showField);
            }

            if(!$this->with){
                $this->with = [];
            }

            $this->with = array_merge($this->with, ['user']);

            $query->with($this->with);

//            $query->where(['status' => Enum::SYSTEM_STATUS_SUCCESS]);

            if($where){
                $query->where($where);
            }

            $query->orderBy(['replytime' => SORT_DESC]);

            $query->asArray();
        });
    }

}