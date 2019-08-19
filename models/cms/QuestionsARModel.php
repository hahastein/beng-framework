<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\AttachmentARModel;
use bengbeng\framework\models\UserARModel;
use yii\db\ActiveQuery;

/**
 * Class QuestionsARModel
 * @property integer $question_id
 * @property string $title 标题（如果不存在标题的，从内容截取25个字做标题）
 * @property string $text 内容
 * @property integer $user_id 用户ID
 * @property integer $reply_count 回复总数
 * @property integer $view_count 浏览总数
 * @property integer $share_count 分享总数
 * @property integer $fav_count 收藏总数
 * @property bool $is_reply 是否可以回复
 * @property bool $show_img 是否给普通用户显示图片
 * @property integer $status 状态 10正常0删除4违规
 * @property integer $createtime 创建时间
 * @property integer $updatetime 修改时间
 * @package bengbeng\framework\models\cms
 */
class QuestionsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_questions}}';
    }

    public function getAnswers(){
        return $this->hasMany(AnswersARModel::className(),['question_id'=>'question_id']);
    }

    public function getImages(){
        return $this->hasMany(AttachmentARModel::className(),['object_id'=>'question_id', 'att_type' => Enum::MODULE_TYPE_FAQS]);
    }

    public function getUser(){
        return $this->hasOne(UserARModel::className(),['user_id'=>'user_id'])->select([
            'nickname',
            'avatar_head',
            'user_id'
        ]);
    }

    public function findAllByCateID($cate_id = 0){
        return $this->findByAll([
            'cate_id' => $cate_id
        ]);
    }

    public function findAllByUserID($user_id){
        return $this->findByAll([
            'user_id' => $user_id
        ]);
    }

    public function findInfoByQuestionID(){

    }

    private function findByAll($where){
        return self::dataSet(function (ActiveQuery $query) use ($where){
            if($this->showField){
                $query->select($this->showField);
            }

            if ($this->with){
                if(!in_array('user', $this->with)){
                    $this->with[] = 'user';
                }
                if(!in_array('images', $this->with)){
                    $this->with[] = 'images';
                }
                $query->with($this->with);
            }

            $query->where(['status' => Enum::SYSTEM_STATUS_SUCCESS]);

            if($where){
                $query->andWhere($where);
            }

            $query->orderBy(['updatetime' => SORT_DESC]);

            $query->asArray();
        });
    }
}